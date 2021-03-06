<?php 
namespace Destiny\LastFm;

use Destiny\Common\Service;
use Destiny\Common\CurlBrowser;
use Destiny\Common\Utils\Date;
use Destiny\Common\Config;
use Destiny\Common\Exception;
use Destiny\Common\MimeType;

/**
 * @method static LastFMApiService instance()
 */
class LastFMApiService extends Service {
    
    /**
     * @param array $options
     * @return CurlBrowser
     */
    public function getLastFMTracks(array $options = array()) {
        return new CurlBrowser ( array_merge ( array (
            'url' => 'http://ws.audioscrobbler.com/2.0/?api_key='. Config::$a ['lastfm']['apikey'] .'&user='. Config::$a ['lastfm']['user'] .'&method=user.getrecenttracks&limit=3&format=json',
            'contentType' => MimeType::JSON,
            'onfetch' => function ($json) {
                if (! $json || isset ( $json ['error'] ) && $json ['error'] > 0 || count ( $json ['recenttracks'] ['track'] ) <= 0) {
                    throw new Exception ( 'Error fetching tracks' );
                }
                foreach ( $json ['recenttracks'] ['track'] as $i => $track ) {
                    // Timezone DST = -1
                    if (! isset ( $track ['@attr'] ) || $track ['@attr'] ['nowplaying'] != true) {
                        if (! empty ( $track ['date'] )) {
                            $json ['recenttracks'] ['track'] [$i] ['date'] ['uts]'] = $track ['date'] ['uts'];
                            $json ['recenttracks'] ['track'] [$i] ['date_str'] = Date::getDateTime ( $track ['date'] ['uts'] )->format ( Date::FORMAT );
                        }
                    } else {
                        $json ['recenttracks'] ['track'] [$i] ['date_str'] = '';
                    }
                }
                return $json;
            } 
        ), $options ) );
    }

}