<?php
	require 'info.php';
	
	function favelaframes_build($action, $settings, $board) {
		// Possible values for $action:
		//	- all (rebuild everything, initialization)
		//	- news (news has been updated)
		//	- boards (board list changed)
		
		Favelaframes::build($action, $settings);
	}

	// Wrap functions in a class so they don't interfere with normal Tinyboard operations
	class Favelaframes {
		public static function build($action, $settings) {
			global $config;
			
			if ($action == 'all') {
				copy('templates/themes/favelaframes/favelaframes.css', $config['dir']['home'] . 'stylesheets/' . $settings['css']);
				file_write($config['dir']['home'] . $settings['file_main'], Favelaframes::homepage($settings));
			}
			
			if ($action == 'all' || $action == 'boards')
				file_write($config['dir']['home'] . $settings['file_sidebar'], Favelaframes::sidebar($settings));
			
			if ($action == 'all' || $action == 'news')
				file_write($config['dir']['home'] . $settings['file_news'], Favelaframes::news($settings));
		}
		
		// Build homepage
		public static function homepage($settings) {
			global $config;
			
			return Element('themes/favelaframes/frames.html', Array('config' => $config, 'settings' => $settings));
		}
		
		// Build news page
		public static function news($settings) {
			global $config;
			
			$query = query("SELECT * FROM ``news`` ORDER BY `time` DESC") or error(db_error());
			$news = $query->fetchAll(PDO::FETCH_ASSOC);
			
			return Element('themes/favelaframes/news.html', Array(
				'settings' => $settings,
				'config' => $config,
				'news' => $news
			));
		}
		
		// Build sidebar
		public static function sidebar($settings) {
			global $config, $board;

			$categories = $config['categories'];
			
			foreach ($categories as &$boards) {
				foreach ($boards as &$board) {
					$title = boardTitle($board);
					if (!$title)
						$title = $board; // board doesn't exist, but for some reason you want to display it anyway
					$board = Array('title' => $title, 'uri' => sprintf($config['board_path'], $board));
				}
			}
			
			return Element('themes/favelaframes/sidebar.html', Array(
				'settings' => $settings,
				'config' => $config,
				'categories' => $categories
			));
		}
	};
	
?>
