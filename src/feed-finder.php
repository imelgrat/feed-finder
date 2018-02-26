<?php

	if (!class_exists('FeedFinder'))
	{
		/**
		 * FeedFinder
		 * This class can be used to extract the URLs of RSS (1.0 and 2.0) and ATOM feeds associated to a page, as well as OPML outline documents.
		 *
		 * The class retrieves a given page (using cURL) and parses its head section to obtain the list of associated RSS, ATOM and OPML links. 
		 * The URLs of the available links are returned in an array, if any. 
		 * Before attempting to retrieve the specified page, the class can check the site's robots.txt file first to see if it is allowed to crawl the site pages. 
		 *
		 * @package FeedFinder
		 * @copyright 2018
		 * @author    Ivan Melgrati
		 * @link https://imelgrat.me
		 * @version   v2.0
		 */
		class FeedFinder
		{
			/**
			 * @var string $url
			 * URL to fetch feeds from
			 */
			protected $url;

			/**
			 * @var string $useragent
			 * User Agent to use during robots.txt and feed fetching operations
			 */
			protected $useragent = 'Googlebot';

			/**
			 * @var boolean $obey_robots
			 * Defines whether to obey robots.txt directives during discovery operations. 
			 * If false, and if crawling is disallowed, no feeds will be returned. 
			 */
			protected $obey_robots;

			/**
			 * Constructor
			 * 
			 * @param string $url URL to fetch feeds from
			 * @param string $useragent User Agent to use during robots.txt and feed fetching operations
			 * @param boolean $obey_robots Defines whether to obey robots.txt directives during discovery operations. 
			 * @return FeedFinder
			 */
			function __construct($url = '', $useragent = 'Googlebot', $obey_robots = true)
			{
				if ($url != '')
				{
					$this->setURL($url);
				}

				if ($useragent != '')
				{
					$this->setUserAgent($useragent);
				}

				$this->setObeyRobots($obey_robots);
			}


			/**
			 * Get the URL to fetch feeds from.
			 *
			 * @return string The URL to fetch feeds from
			 */
			public function getURL()
			{
				return $this->url;
			}

			/**
			 * Get the User Agent to use during robots.txt and feed fetching operations
			 *
			 * @return string The User Agent to use during robots.txt and feed fetching operations
			 */
			public function getUserAgent()
			{
				return $this->useragent;
			}

			/**
			 * Get whether to obey robots.txt directives during discovery operations.
			 *
			 * @return string Whether to obey robots.txt directives during discovery operations.
			 */
			public function getObeyRobots()
			{
				return $this->obey_robots;
			}
			/**
			 * Set the URL to fetch feeds from.
			 *
			 * @param  string $url The URL to fetch feeds from
			 * @return FeedFinder
			 */
			public function setURL($url)
			{
				if ($url != '')
				{
					$this->url = trim($url);
				}

				return $this;
			}

			/**
			 * Set the User Agent to use during robots.txt and feed fetching operations
			 *
			 * @param  string $useragent The User Agent to use during robots.txt and feed fetching operations
			 * @return FeedFinder
			 */
			public function setUserAgent($useragent)
			{
				if ($useragent != '')
				{
					$this->useragent = trim($useragent);
				}

				return $this;
			}

			/**
			 * Set whether to obey robots.txt directives during discovery operations.
			 *
			 * @param  string $obey_robots Whether to obey robots.txt directives during discovery operations.
			 * @return FeedFinder
			 */
			public function setObeyRobots($obey_robots = true)
			{
				$this->obey_robots = $obey_robots ? true : false;

				return $this;
			}

			/**
			 * Fetch Contents from URL. Uses cURL, with file_get_contents() fallback
			 *
			 * @return string
			 */
			protected function fetchURL()
			{

				if (in_array('curl', get_loaded_extensions()))
				{
					$options = array(
						CURLOPT_RETURNTRANSFER => true, // return web page
						CURLOPT_HEADER => false, // don't return headers
						CURLOPT_FOLLOWLOCATION => true, // follow redirects
						CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
						CURLOPT_ENCODING => "", // handle compressed
						CURLOPT_USERAGENT => $this->useragent, // name of client
						CURLOPT_AUTOREFERER => true, // set referrer on redirect
						CURLOPT_CONNECTTIMEOUT => 120, // time-out on connect
						CURLOPT_TIMEOUT => 120, // time-out on response
						);

					$ch = curl_init($this->getURL());
					curl_setopt_array($ch, $options);
					$html = @curl_exec($ch);
				}
				else
				{
					$html = @file_get_contents($this->getURL(), false, $context);
				}
				return $html;
			}

			/**
			 * Find whether robots are allowed to fetch content from the site by testing the robots.txt file
			 *
			 * @return boolean
			 */
			public function robotsAllowed()
			{
				$url = $this->getURL();
				$useragent = $this->getUserAgent();

				if ($url == '')
				{
					$url = $this->url;
				}
				else
				{
					$this->url = $url;
				}

				if ($useragent == '')
				{
					$useragent = $this->useragent;
				}
				else
				{
					$this->useragent = $useragent;
				}

				if ($url == '' || !filter_var($url, FILTER_VALIDATE_URL))
				{
					return false;
				}

				// Parse url to retrieve host and path
				$parsed = parse_url($url);

				$agents = array(preg_quote('*'));

				if ($useragent)
				{
					$agents[] = preg_quote($useragent);
				}

				$agents = implode('|', $agents);

				// location of robots.txt file
				$robotstxt = trim($this->fetchURL("http://{$parsed['host']}/robots.txt"));

				// If there's no robots.txt file, we're allowed
				if ($robotstxt == '')
				{
					return true;
				}
				$robotstxt = explode("\n", $robotstxt);

				$rules = array();
				$ruleapplies = false;

				foreach ($robotstxt as $line)
				{
					// Skip blank lines
					if (!$line = trim($line))
					{
						continue;
					}

					// Following rules only apply if User-agent matches $useragent || '*'
					if (preg_match('/User-agent: (.*)/i', $line, $match))
					{
						$ruleapplies = preg_match("/($agents)/i", $match[1]);
					}

					// Walk through all robots.txt rules
					if ($ruleapplies && preg_match('/Disallow:(.*)/i', $line, $regs))
					{
						// An empty rule implies full access - no further tests required
						if (!$regs[1])
						{
							return true;
						}

						// Add rules that apply to array for testing
						$rules[] = preg_quote(trim($regs[1]), '/');
					}
				}

				foreach ($rules as $rule)
				{
					// If robots.txt disallows User Agent, no further testing required
					if (preg_match("/^$rule/", $parsed['path']))
					{
						return false;
					}
				}

				// Page is not disallowed
				return true;
			}


			/**
			 * Get an array of RSS, ATOM and OPML links by parsing HTML
			 *
			 * @return string Array
			 */
			function getFeeds()
			{
				$url_list = array();

				$url = $this->getURL();

				// Check whether an URL was provided and URL is valid. If not, return an empty list.
				if (trim($url) == '' || !filter_var($url, FILTER_VALIDATE_URL))
				{
					return $url_list;
				}

				// If we're not allowed to fetch content and want to obey robots.txt directives, return empty array
				if (($this->getObeyRobots() == true) && !$this->robotsAllowed())
				{
					return $url_list;
				}
				else
				{
					// Get HTML page content
					$html = trim($this->fetchURL($url));

					if ($html != '')
					{
						// Search through the HTML, save all <link> tags
						preg_match_all('/<link\s+(.*?)\s*\/?>/si', $html, $matches);
						$links = $matches[0];
						$final_links = array();

						// Parse all <link> tags and extract attributes using a regular expression.
						foreach ($links as $row)
						{
							preg_match_all('/([a-z0-9_\:\-]*)\s*?=\s*?([`\'"]?)(.*?)\2\s+/mi', $row, $attributes, PREG_SET_ORDER, 0);
                            $final_link = array();
							foreach ($attributes as $attribute)
							{
								if (isset($attribute[1]))
								{
									$final_link[$attribute[1]] = $attribute[3];
								}
							}
							$final_links[] = $final_link;
						}

						// Now figure out which ones point to the RSS+ATOM feeds and OPML outline files
						foreach ($final_links as $test_link)
						{
							$href = '';
							if (strtolower($test_link['rel']) == 'alternate' || strtolower($test_link['rel']) == 'outline')
							{
								if (strtolower($test_link['type']) == 'application/rss+xml')
								{
									$href = $test_link['href'];
								}
                
								if (!$href and strtolower($test_link['type']) == 'text/xml')
								{
									// kludge to make the first version of this still work
									$href = $test_link['href'];
								}
								if (!$href and strtolower($test_link['type']) == 'application/atom+xml')
								{
									// Find ATOM feeds
									$href = $test_link['href'];
								}

								if (!$href and in_array(strtolower($test_link['type']), array(
									'text/x-opml',
									'application/xml',
									'text/xml')) and preg_match("/\.opml$/", $test_link['href']))
								{
									// Find OPML outlines
									$href = $test_link['href'];
								}

								if ($href)
								{
									if (strstr($href, "http://") !== false || strstr($href, "https://") !== false)
									{
										// If it's an absolute URL
										$full_url = $href;
									}
									else
									{
										// Otherwise, make it an absolute one by adding URL scheme and host
										$url_parts = parse_url($url);

										$full_url = "$url_parts[scheme]://$url_parts[host]";
										if (isset($url_parts['port']))
										{
											$full_url .= ":$url_parts[port]";
										}

										if ($href{0} != '/')
										{
											// It's a relative link on the domain
											$full_url .= dirname($url_parts['path']);
											if (substr($full_url, -1) != '/')
											{
												// If the last character isn't a '/', add it
												$full_url .= '/';
											}
										}
										$full_url .= $href;
									}

									// Only add the feed URL if not already on the list
									if (!in_array($full_url, $url_list))
									{
										$url_list[] = $full_url;
									}
								}
							}
						}
					}
				}
				return $url_list;
			}
		}
	}

?>