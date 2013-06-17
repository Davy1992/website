<?php

namespace Destiny\Action\Fantasy;

use Destiny\Service\LeagueApiService;
use Destiny\Utils\Http;
use Destiny\MimeType;
use Destiny\Session;
use Destiny\Application;
use Destiny\Config;

class Ingame {

	public function execute(array $params) {
		$app = Application::instance ();
		$cacheDriver = $app->getCacheDriver ();
		$ingame = null;
		foreach ( Config::$a ['lol'] ['summoners'] as $summoner ) {
			if ($summoner ['track'] == false) {
				continue;
			}
			$ingame = $cacheDriver->fetch ( 'ingame.' . $summoner ['id'] );
			if ($ingame != null && $ingame ['success'] == true && $ingame ['data'] != null) {
				$ingame = $ingame ['data'];
				// Abililty to send the game id, if it is still ingame, send a not modified response
				if (isset ( $params ['gameId'] ) && intval ( $params ['gameId'] ) == $ingame ['gameId']) {
					Http::status ( Http::STATUS_NOT_MODIFIED );
					Http::header ( Http::HEADER_CONNECTION, 'close' );
				}
				break;
			} else {
				$ingame = null;
			}
		}
		Http::header ( Http::HEADER_CONTENTTYPE, MimeType::JSON );
		Http::sendString ( json_encode ( $ingame ) );
	}

}