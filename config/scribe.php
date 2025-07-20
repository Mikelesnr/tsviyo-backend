Class "Knuckles\Scribe\Extracting\Strategies\StaticData" not found
Error
in /var/www/html/config/scribe.php (line 94)
], 'strategies' => [ 'metadata' => [], 'headers' => [ Strategies\StaticData::withSettings(data: [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ]), ], 'urlParameters' => [],
in /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Bootstrap/LoadConfiguration.php require (line 96)
in /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Bootstrap/LoadConfiguration.php -> Illuminate\Foundation\Bootstrap\{closure} (line 96)
in /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Bootstrap/LoadConfiguration.php -> loadConfigurationFile (line 77)
in /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Bootstrap/LoadConfiguration.php -> loadConfigurationFiles (line 38)
in /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Application.php -> bootstrap (line 341)
in /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php -> bootstrapWith (line 186)
in /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php -> bootstrap (line 170)
in /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php -> sendRequestThroughRouter (line 144)
in /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Application.php -> handle (line 1219)
Application->handleRequest(object(Request))
in /var/www/html/public/index.php (line 20)