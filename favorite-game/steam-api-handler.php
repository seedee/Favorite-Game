<?php
    header("Access-Control-Allow-Origin: https://gamebanana.com");
    header("Access-Control-Allow-Methods: GET");
    header("Content-Type: application/json");

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if (isset($_GET["steamId64"]) && isset($_GET["appId"])) {
            require_once("/home2/lebdanzu/config/config.php"); //STEAM_API_KEY
            $curl = curl_init("https://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=" . STEAM_API_KEY . "&steamid=" . $_GET["steamId64"] . "&include_appinfo=1&include_played_free_games=1&format=json");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //Return as string

            $steamResponse = curl_exec($curl);

            if ($steamResponse !== false) { //If HTTP response successful
                curl_close($curl);
        
                $steamData = json_decode($steamResponse, true);
                
                if ($steamData && isset($steamData["response"]["games"])) { //If data is not null and contains games array
                    $games = $steamData["response"]["games"];
                    $game = array_search($_GET["appId"], array_column($games, "appid"));
                
                    if ($game !== false) { //If appId is found in games array
                        $data = [];
                        $data["gameName"] = $games[$game]["name"];
                    
                        if (isset($_GET["displayHours"]) && $_GET["displayHours"] == "true")
                            $data["hoursPlayed"] = floor($games[$game]["playtime_forever"] / 60);
                        echo(json_encode($data, JSON_PRETTY_PRINT)); //Response
                    }
                    else //If appId is not found in games array
                        echo(json_encode(array("error" => "AppID " . $_GET["appId"] . " not in user library"), JSON_PRETTY_PRINT)); //Response
                }
                else //If data is null or does not contain games array
                    echo(json_encode(array("error" => "Unable to parse user library"), JSON_PRETTY_PRINT));
            }
            else { //If HTTP response fails
                $error = curl_error($curl);
                curl_close($curl);
                echo(json_encode(array("error" => "Curl error: " . $error), JSON_PRETTY_PRINT));
            }
        }
        else //If steamId64 or appId are not set
            echo(json_encode(array("error" => "Unable to parse SteamID64 or AppID"), JSON_PRETTY_PRINT));
    }
?>
