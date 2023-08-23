<module id="FavoriteGameModule" class="PageModule">
    <h2>
        <a href="/apps/930">Favorite Game</a>
        <?php
            if ($_GET["_idProfile"] === $_GET["_idMember"])
                echo("<spriteicon></spriteicon>");
        ?>
    </h2>
    <div class="Content">
        <div class="game">
            <img class="banner" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAcwAAAABCAYAAABT2p5YAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAYSURBVEhLYxgFo2AUjIJRMApGASHAwAAABzEAAUt3388AAAAASUVORK5CYII=" alt="Loading">
            <span class="name"></span>
            <span class="hours"></span>
            <span class="hours-caption"></span>
        </div>
        <?php
        if ($_GET["_idProfile"] === $_GET["_idMember"]) {
            echo <<<HTML
            <div class="game-settings">
                <form class="MainForm" accept-charset="utf-8"> 
                    <div class="InputsWrapper">
                        <fieldset>
                            <div class="InputWrapper">
                                <label>
                                    <input id="UseSteam" name="useSteam" type="checkbox">
                                    <span>Use Steam</span>
                                </label>
                            </div>
                        </fieldset>
                        <div id="NonSteam">
                            <fieldset>
                                <legend>
                                    <span class="Title">Name</span>
                                </legend>
                                <div class="InputHelp">The name of the game.</div>
                                <div class="InputWrapper">
                                    <input id="GameName" name="gameName" type="text" size="20" maxlength="50">
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>
                                    <span class="Title">Banner</span>
                                </legend>
                                <div class="InputHelp">URL to the banner image.</div>
                                <div class="InputWrapper">
                                    <input id="BannerUrl" name="bannerUrl" type="text" size="50">
                                </div>
                            </fieldset>
                        </div>
                        <div id="Steam">
                            <fieldset>
                                <legend>
                                    <span class="Title">SteamID64</span>
                                </legend>
                                <div class="InputHelp">Your SteamID64 (Dec). You can use <a target="_blank" href="https://steamid.io/">steamid.io</a></div>
                                <div class="InputWrapper">
                                    <input id="SteamId64" name="steamId64" type="text" size="17" maxlength="17">
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>
                                    <span class="Title">AppID</span>
                                </legend>
                                <div class="InputHelp">The AppID of the game.</div>
                                <div class="InputWrapper">
                                    <input id="AppId" name="appId" type="text" size="10" maxlength="10">
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>
                                    <span class="Title">Total Playtime</span>
                                </legend>
                                <div class="InputHelp">Display total hours on record.</div>
                                <div class="InputWrapper">
                                    <label>
                                        <input id="DisplayHours" name="displayHours" type="checkbox">
                                        <span>Enabled</span>
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                        <fieldset class="Submit">
                            <button id="SaveGameSettings" class="IconButton" type="button">
                                <spriteicon class="MiscIcon DiskIcon"></spriteicon>
                                <span>Save</span>
                            </button>
                        </fieldset>
                    </div>
                </form>
            </div>
            HTML;
        }
        ?>
    </div>
</module>
<script type="text/javascript"> //General script
    const moduleParent = document.querySelector("#tpm__sFavoriteGame");

    if (moduleParent !== null)
        moduleParent.removeAttribute("style");
    const game = document.querySelector("#FavoriteGameModule .game");
    game.style.display = "grid";
    game.style.gridTemplateColumns = "2fr 3fr";
	game.style.gap = "0 1rem";
    game.style.lineHeight = "1.5rem";

    const hours = document.querySelector("#FavoriteGameModule .hours");
    hours.style.paddingTop = "1rem";
    hours.style.fontSize = "var(--DefaultXxLargeFontSize)";

    const hoursCaption = document.querySelector("#FavoriteGameModule .hours-caption");
	hoursCaption.style.gridColumn = "1";
    hoursCaption.style.fontSize = "var(--DefaultMediumFontSize)";

    const name = document.querySelector("#FavoriteGameModule .name");
    name.style.paddingTop = "0.5rem";
    name.style.overflow = "hidden";
    name.style.fontSize = "var(--DefaultXLargeFontSize)";

    const image = document.querySelector("#FavoriteGameModule .banner");
    image.style.width = "100%";
    image.style.objectFit = "cover";
    image.style.objectPosition = "center";

    image.ondragstart = function(e) {
        e.preventDefault();
    };

    function resizeImage() {
        const aspectRatio = 92 / 43;
        const newHeight = Math.round(image.offsetWidth / aspectRatio);
        image.style.height = newHeight + "px";
    }

    image.addEventListener("load", function() {
        resizeImage();
    });

    const resizeObserver = new ResizeObserver(() => {
        resizeImage();
    });

    resizeObserver.observe(image);

    function getSteamData(steamId64, appId, displayHours) {
        return fetch("https://lebdan.zumesite.com/favorite-game/steam-api-handler.php?steamId64=" + steamId64 + "&appId=" + appId + "&displayHours=" + displayHours, {
            mode: "cors"
        })
        .then(response => {
            if (response.ok)
                return response.json();
            else
                throw new Error("Error loading game settings: Network response was not ok");
        })
        .catch(error => {
            console.error("Error loading data for SteamID64 " + steamId64 + ": " + error);
            throw error;
        });
    }

    function updateUI(useSteam, gameName, bannerUrl, steamId64, appId, displayHours, profileOwner) {
        Promise.resolve()
            .then(() => {
                if (useSteam) { //If using Steam
                    return getSteamData(steamId64, appId, displayHours)
                        .then(steamData => {
                            if (Object.keys(steamData).length === 1 && steamData.hasOwnProperty("error")) { //If Steam data returns error
                                console.error("Error loading Steam data: " + steamData["error"]);
                                gameName = "";
                                bannerUrl = "";
                                hours.style.display = "none";
                                hoursCaption.style.display = "none";
                            }
                            else {
                                gameName = steamData["gameName"];
                                bannerUrl = "https://cdn.cloudflare.steamstatic.com/steam/apps/" + appId + "/header.jpg";

                                if (steamData.hasOwnProperty("hoursPlayed")) {
                                    hours.textContent = steamData["hoursPlayed"].toLocaleString();
                                    hours.style.display = "block";
                                    hoursCaption.style.display = "block";
                                    hoursCaption.textContent = "Hours played";
                                }
                                else {
                                    hours.style.display = "none";
                                    hoursCaption.style.display = "none";
                                }
                            }
                            return { gameName, bannerUrl };
                        });
                }
                else { //If not using Steam
                    hours.style.display = "none";
                    hoursCaption.style.display = "none";
                    return { gameName, bannerUrl };
                }
            })
            .then(({ gameName, bannerUrl }) => {
                if (gameName.length > 0)
                    name.textContent = gameName;
                else
                    name.textContent = "Half-Life 2";

                if (bannerUrl.length > 0)
                    image.src = bannerUrl;
                else
                    image.src = "https://cdn.cloudflare.steamstatic.com/steam/apps/220/header.jpg";
                image.style.maxWidth = image.naturalWidth + "px";
                image.alt = name.textContent;

                if (profileOwner) { //If game settings menu visible
                    const useSteam_Input = document.querySelector("#FavoriteGameModule .game-settings #UseSteam");
                    useSteam_Input.checked = useSteam;

                    const gameName_Input = document.querySelector("#FavoriteGameModule .game-settings #GameName");
                    gameName_Input.value = useSteam ? "" : gameName;

                    const bannerUrl_Input = document.querySelector("#FavoriteGameModule .game-settings #BannerUrl");
                    bannerUrl_Input.value = useSteam ? "" : bannerUrl;

                    const steamId64_Input = document.querySelector("#FavoriteGameModule .game-settings #SteamId64");
                    steamId64_Input.value = useSteam ? steamId64 : "";

                    const appId_Input = document.querySelector("#FavoriteGameModule .game-settings #AppId");
                    appId_Input.value = useSteam ? appId : "";

                    const displayHours_Input = document.querySelector("#FavoriteGameModule .game-settings #DisplayHours");
                    displayHours_Input.checked = useSteam && displayHours;
                }
            });
    }

    function loadGameSettings() {
        image.classList.remove("ImgError");

        <?php
            if ($_GET["_idProfile"] === $_GET["_idMember"])
                echo("const profileOwner = true;");
            else
                echo("const profileOwner = false;");
        ?>
        fetch("https://gamebanana.com/apiv11/App/930/CustomConfig/<?php echo($_GET["_idProfile"]); ?>", {
                headers: {
                    "Accept": "application/json, text/plain, */*"
                }
            })
            .then(response => {
                if (response.ok)
                    return response.json();
                else
                    throw new Error("Error loading game settings: Network response was not ok");
            })
            .then(data => {
                const configData = data;
                
                if ("_aUseSteam" in configData && "_aGameName" in configData && "_aBannerUrl" in configData && "_aSteamId64" in configData && "_aAppId" in configData && "_aDisplayHours" in configData) //If valid app configuration found
                    updateUI(configData["_aUseSteam"], configData["_aGameName"], configData["_aBannerUrl"], configData["_aSteamId64"], configData["_aAppId"], configData["_aDisplayHours"], profileOwner);
                else { //If no valid app configuration found
                    updateUI(false, "", "", "", "", false, profileOwner);
                    console.error("Error loading game settings: No valid settings found");
                }
            })
            .catch(error => {
                updateUI(false, "", "", "", "", false, profileOwner);
                console.error("Error loading game settings: " + error);
                throw error;
            });
    }

    loadGameSettings();
</script>
<?php
    if ($_GET["_idProfile"] === $_GET["_idMember"]) {
        echo <<<HTML
        <script type="text/javascript"> //Settings script
            const spriteIcon = document.querySelector("#FavoriteGameModule h2 > spriteicon");
            spriteIcon.classList.add("MiscIcon");
            spriteIcon.classList.add("SpannerIcon");
            spriteIcon.style.marginLeft = ".5rem"; //Value of .Cluster { --ClusterGap }
            spriteIcon.style.transition = "opacity .3s cubic-bezier(.25,.46,.45,.94)";
            spriteIcon.style.userSelect = "none";
            spriteIcon.style.cursor = "pointer";

            const opacity = "0.5";
            spriteIcon.style.opacity = opacity;

            const favoriteGameModule = document.querySelector("#FavoriteGameModule");
            favoriteGameModule.addEventListener("mouseover", () => {
                spriteIcon.style.opacity = "1";
            });
            favoriteGameModule.addEventListener("mouseout", () => {
                spriteIcon.style.opacity = opacity;
            });

            const gameSettings = document.querySelector("#FavoriteGameModule .game-settings");
            gameSettings.style.display = "none";
            
            const useSteam_Input = document.querySelector("#FavoriteGameModule .game-settings #UseSteam");
            const nonSteam = document.querySelector("#FavoriteGameModule .game-settings #NonSteam");
            const steam = document.querySelector("#FavoriteGameModule .game-settings #Steam");
            spriteIcon.addEventListener("click", () => {
                if (gameSettings.style.display === "none") {
                    spriteIcon.classList.remove("MiscIcon", "SpannerIcon");
                    spriteIcon.classList.add("LeftArrowIcon", "SmallWhiteArrowIcon");
                    game.style.display = "none";
                    gameSettings.style.display = "block";

                    if (useSteam_Input.checked) {
                        steam.style.display = "block";
                        nonSteam.style.display = "none";
                    }
                    else {
                        steam.style.display = "none";
                        nonSteam.style.display = "block";
                    }
                }
                else {
                    spriteIcon.classList.remove("LeftArrowIcon", "SmallWhiteArrowIcon");
                    spriteIcon.classList.add("MiscIcon", "SpannerIcon");
                    game.style.display = "grid";
                    gameSettings.style.display = "none";
                }
            });

            useSteam_Input.addEventListener("change", (e) => {
                if (e.target.checked) {
                    steam.style.display = "block";
                    nonSteam.style.display = "none";
                }
                else {
                    steam.style.display = "none";
                    nonSteam.style.display = "block";
                }
            });

            const saveGameSettings = document.querySelector("#FavoriteGameModule .game-settings #SaveGameSettings");
            saveGameSettings.addEventListener("click", () => {
                const gameSettingsData = new FormData(document.querySelector("#FavoriteGameModule .game-settings > form"));
                const useSteam = gameSettingsData.get("useSteam") === "on";
                const data = {
                    "_aData": {
                        "_aUseSteam": useSteam,
                        "_aGameName": useSteam ? "" : gameSettingsData.get("gameName"),
                        "_aBannerUrl": useSteam ? "" : gameSettingsData.get("bannerUrl"),
                        "_aSteamId64": useSteam ? gameSettingsData.get("steamId64") : "",
                        "_aAppId": useSteam ? gameSettingsData.get("appId") : "",
                        "_aDisplayHours": useSteam && gameSettingsData.get("displayHours") === "on"
                    }
                };

                const request = JSON.stringify(data);
                fetch("https://gamebanana.com/apiv11/App/930/CustomConfig", {
                        method: "PATCH",
                        body: request
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        loadGameSettings();
                        spriteIcon.dispatchEvent(new Event("click"));
                    })
                    .catch(error => {
                        console.error("Error saving game settings: " + error)
                        throw error;
                    });
            });
        </script>
        HTML;
    }
?>