# Favorite Game
This is a web app project for GameBanana which displays on user profiles as a custom module. It shows a favorite game including total hours on record from your Steam profile. It uses the built-in CustomConfig API to store user data directly on GameBanana, and the GetOwnedGames method from the Steam Web API to retrieve game data. 

## Usage on-site
1. Go to the [app page](https://gamebanana.com/apps/930) and click "Install App".
2. Add the module in your Profile WYSIWYG Editor, or include `<?= $d["_aThirdPartyModules"]["_sFavoriteGame"] ?>` in your Profile Template.
3. If using Steam features, set your Steam profile and games library to public.

## How to install
Set CORS-related HTTP headers to allow GET requests.
```apacheconf
Access-Control-Allow-Origin "https://gamebanana.com"
Access-Control-Allow-Methods "GET"
Access-Control-Allow-Headers "Content-Type"
```
In `module.php` set your own domain for the absolute path to `steam-api-handler.php`, it must be used because the app is embedded directly into GameBanana.
```php
function getSteamData(steamId64, appId, displayHours) {
    return fetch("https://lebdan.zumesite.com/favorite-game/steam-api-handler.php
```
In `steam-api-handler.php` set the include to the corresponding file with your API key.
```php
require_once("/home2/lebdanzu/config/config.php"); //STEAM_API_KEY
```
The API key is declared in `config.php`.
```php
<?php
    const STEAM_API_KEY = "00000000000000000000000000000000";
?>
```

## Features
The name and preview banner are displayed with a similar design to the favorite game showcase on Steam profiles. Hours played are optional and will be visible if you've linked with a Steam ID.

![Steam game](https://images.gamebanana.com/img/ss/apps/643b5c0a780cc.jpg)
![Non-Steam game](https://images.gamebanana.com/img/ss/apps/643b5c0a6e454.jpg)

Detailed control panel accessible with a button that's only displayed for the profile owner.
![Steam game settings](https://images.gamebanana.com/img/ss/apps/643b5c0a9d7be.jpg)
![Non-Steam game settings](https://images.gamebanana.com/img/ss/apps/643b5c0a60d95.jpg)

Generic game is displayed if the module is configured.
![Default unconfigured game](https://images.gamebanana.com/img/ss/apps/643b5c0a67faf.jpg)