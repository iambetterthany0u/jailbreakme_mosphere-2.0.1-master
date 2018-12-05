<!DOCTYPE html>
<html manifest="./caching.manifest">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-title" content="Mosphere JB ">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		<link rel="apple-touch-icon" href="apple-touch-icon.png">
		<link rel="shortcut icon" href="apple-touch-icon.png">
		<title>Jailbreakme Unified</title>
		<link rel="stylesheet" href="style.css"/>
		
		

<link href="./splash4.png" sizes="2048x2732" rel="apple-touch-startup-image" />
<link href="./splash4.png" sizes="1668x2224" rel="apple-touch-startup-image" />
<link href="./splash4.png" sizes="1536x2048" rel="apple-touch-startup-image" />
<link href="./splash4.png" sizes="1125x2436" rel="apple-touch-startup-image" />
<link href="./splash4.png" sizes="1242x2208" rel="apple-touch-startup-image" />
<link href="./splash4.png" sizes="750x1334" rel="apple-touch-startup-image" />
<link href="./splash4.png" sizes="640x1136" rel="apple-touch-startup-image" />


		
		
		
		<script src="modular.js" type="text/javascript"></script>
		<script type="text/javascript">

			/* Common functions */
            function strip(html){
               var doc = new DOMParser().parseFromString(html, 'text/html');
               return doc.body.textContent || "";
            }

            //Overwrite document.write to write to our callback element
            document.write = function(x){
                document.getElementById('status').innerHTML+=x;
            };

             //Simple function to write formatted strings to the callback element
            function puts(str) {
                console.log('[WebsploitFramework][Main]: '+strip(str.toString()));
                document.write(str.toString()+"<br>");
            }

            //Simply create an object, as OOP is always better
            var Jailbreakme = {
            	initialized: false, 
            	callback: function(id){
            		return document.getElementById(id);
            	}
            };

            //This should always be called first
			Jailbreakme.init = function()
			{

				//loads the required modules for this file and sets the modulebase
				var load_modules = function()
				{
					console.log("Initializing modules...");
					ModularJS.config.modulebase = "modules";
					setTimeout(function(){
			            Import('libcrypto');
			            Import('libsploit')
			            Import('libmacho');
					},0.01); //threaded
				};

				//Loads the assetswallpaper asynchronously and updates the background image (on the main thread), this to make sure no delay in loading is caused
				var update_wallpaper = function()
				{
					console.log("Updating wallpaper...");
					setTimeout(
						function(){
							document.body.setAttribute('style', 
								'background: #000 url(\'assets/wallpaper3.png\');\
								 background-size: cover;\
								 background-repeat: no-repeat;'
							);
						}, 
					0.01); //threaded
				};

				//Retrieves any old logs and displays them on the logging element
				var init_log = function()
				{
					console.log("Retrieving old logs...");
					this.log = false;

					//attempt to retrieve the log from the local storage
					if(window.location.href.split('file://').length==1) { //we check if the url contains file:// (1 = not contains, 2 = contains) this because localStorage is prohibited in local files
						if(localStorage){ //check if the browser supports localstorage
							this.log = localStorage.getItem('log') || false; //try to read the last log from localstorage
						}
					}

					//if we have any old logs, log them to our output element
					if(this.log){
						
						var log_el = document.getElementById('log'); //reference the output element

						if(log_el) { //if we have a reference we can update it with our new log
							log_el.innerHTML="<strong>LAST LOG</strong><br>============<br>"+this.log.replace(/\n/g, "<br>"); //We use innerHTML to style the logs, be aware that any log write operation containing html is not filtered for XSS
						}
					}
				};

				//Initializes the crash counter for people who keep spamming without actually using their brains
				var init_crash_watchdog = function()
				{
					console.log("Initializing anti-wen-eta-kid crash watchdog...");

					//register a global enum of stupidities
					window.kStupidity = {
						Smart: 0,
						Dumb: 1,
						Stupid: 2,
						WenETABeggar: 3
					};

					window.stupidity = window.kStupidity.Smart; //at first, before repeatedly smashing the buttons as a mad man, the user is still smart
				};

				//We call the above initialization methods
				try {
					load_modules();
					update_wallpaper();
					init_log();
					init_crash_watchdog(); 
				} 
				catch(exc) //if we end up here, we obviously failed to initialize
				{
					alert("exception occured.\nStack trace: \n"+exc.stack + "\nreason: "+exc.message);
					return;
				}

				this.initialized = true; //We should be initialized now.
			};

			//clears the displayed exploit progress
			Jailbreakme.clearcallback = function() {
				if(!this.callback('status')) return;
				this.callback('status').innerText="";
			};

			//increase counter to trigger a bug in the handling of URI schemes
			Jailbreakme.raise_stupidity = function() {
				if(window.stupidity == window.kStupidity.Stupid) alert('You like pressing some buttons eh? Well I dare you to press it one more time, I\'ll crash your safari. And remember, patience is a virtue!'); //warn the user
				if(window.stupidity == window.kStupidity.WenETABeggar) window.location.href = "denial.html"; //redirect to the bug
				window.stupidity++; //raise the actual stupidity
			};

			//__main entry
			Jailbreakme.go = function(sender)
			{

				if(!this.initialized) this.init(); //check if we are initialized, otherwise make sure that we are

				if(sender) { //if we do not have a sender (e.g.: range element) we are trying to jailbreak directly, that should be avoided, we want the user to interact and make sure he is aware of what he is doing
					
					if(sender.value == 1) {
						
						var support = document.getElementById('support'); //get a reference to the support element

						if(support != undefined) support.remove(); //in case we have a reference, remove it

						console.log('[WebsploitFramework][Main]: '+'Starting...');

						Jailbreakme.clearcallback(); //clear any previous output or messages on the output element

						var ret = sploit_main(); //try to fire up the device detection->strategy selection->suitable exploit

						if(!ret) this.raise_stupidity(); //if the exploit determination failed, then we raise the stupidity of the user

						sender.value = 0; //reset the sender to it's default value, otherwise a range slider would remain on a slided state.

						return ret; //return the return value of the exploit

					} else { //whether the value is 0 or higher then 1 we will end up here, on a range slider element a slide event making the value 0 means the user cancelled the exploit

						var stdout = document.getElementById('status'); //get a reference to the status element, which is our output element
						stdout.innerText = "Jailbreak has been canceled."; //update the status notifyinf the user that the exploit's been cancelled

					}
				} else {
					//this function must be called with a sender thus we do nothing here
				}

			};


			//__load entry
			window.onload = function() {
				try {
					Jailbreakme.init(); //attempt to initialize the jailbreak
				} catch(exc)
				{
					alert("Application initialization failed.\nReason:\n"+exc);
				}
			};

			//Wrapper for the Jailbreakme.go function in a bit more high level. The input element calls this
			var go = function(sender)
			{
				var ret = false;
				try {
					ret = Jailbreakme.go(sender);
				} catch(exc)
				{
					puts("Something went wrong during the exploit<br>Reason:<br>"+exc);
					ret = false;
				}
				return ret;
			};
		</script>
	</head>
	<body>
		<main>
		
			<section>
				
				<span id="clock" ><?php
echo "<div>" . date("h:i") . "</div>";
?></span>



<p id='title'> JBMe Mosphere</p>
<p id='log'>Supported: iOS 4 up to 12.0.1, with some exceptions</p>
				<p id="status">
					Welcome to JaillbreakME Mosphere, have a nice time!
				</p>
			</section>
		</main>
		<footer>
			<section>
				<!-- The element below is used for starting jailbreakme, it can be used in two ways: by clicking it (onclick) and by sliding it (onchange) -->
				<input id="slidetounlock" type="range" value="0" max="1" placeholder="..." onchange="go(this)">
			</section>
</br>
		</footer>
		<!--
			PLEASE NOTE: 
			This program is licensed under the license as actively updated on GitHub https://github.com/userlandkernel/jailbreakme-unified.
			Please respect to work of all individuals involved in the making or the contribution to this framework by giving credit conform the license.
			This software has been written by Sem Voigtländer and consists of multiple exploits by great minds as credited in the license on GitHub.
			This program is not intended to harm your device in any way, nor will it be used for data exfiltration or remote administration purposes.
			Sem Voigtländer and the other contributors and exploit authors are not responsible for loss of data, damage to your device or loss of warranty as result of breaking with the EULA of Apple Inc.
		-->
	</body>
</html>