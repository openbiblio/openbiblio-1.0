// JavaScript Document
"use strict";

const version = "v0.3";
const staticCacheName = version + "-staticFiles";
//const <?php echo "themeDir = '$ThemeDir'\n";
const themeDir = "themes/default";
//const <?php echo "localeDir = '$LocaleDir'\n";
const localeDir = "locale/en";

// at startup                                                                                                                                                                                                                    s
addEventListener ('install', installEvent => {
  console.log ("The service worker is installing...");
  console.log ("theme dir = ../themes/" + themeDir)
  console.log ("locale dir = " + localeDir)
  skipWaiting(); // service worker takes control immediately after install
  installEvent.waitUntil (
    caches.open (staticCacheName)
    .then(staticCache => {
      return staticCache.addAll([
		"./opac/index.php",
        "./shared/normalize.css",
        "./shared/style.css",
        "./shared/jquery/jquery-3.2.1.min.js",
        "./shared/jsLibJs.php",
		"./shared/global_constants.php",
		"./"+themeDir+"/theme.css",
		"./"+themeDir+"/header.php",
		"./"+localeDir+"/trans.php",
      ]);  // end return addAll()
    })  // end open then
  );  // end waitUntil()
}); // end addEventListener

addEventListener ('activate', activateEvent => {
  console.log ("new cache being activatd..., old caches are being deleted.");
  activateEvent.waitUntil (
		caches.keys ()
		.then (cacheNames => {
			return Promise.all (
				cacheNames.map (cacheName => {
					console.log("considering '"+cacheName+"'")
					if (cacheName != staticCacheName) {
					console.log("deleting '"+cacheName+"'")
						return caches.delete (cacheName);
					}  // end if
				})  // end map
			);  // end return Promise.all
		})  // end keys then
		.then ( () => {
			return clients.claim ();
		})  // end then
	);  // end waitUntil
});  // end add EventListener

// when the browser requests a file
addEventListener ('fetch', fetchEvent => {
  console.log("The service worker is listening...");
  const request = fetchEvent.request
  console.log (request)
  fetchEvent.respondWith (
    // first, look in the cache
    caches.match (request)
    .then (responseFromCache => {
      if (responseFromCache) {
        return responseFromCache;
      }  // end if
      // otherwise fetch from network
      return fetch (request)
      .catch (error => {
	    // show a fallback page instead
      	return caches.match ("/offline.html");
	  }); // end fetch catch and return
	})  //end match
  );  // end respondWith
}); // end addEventListener
