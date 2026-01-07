    <?php 
    include_once __DIR__ . "/../Config/Database.php";
    include_once __DIR__ . "/../App/Services/TokenService.php";
    include_once __DIR__ . "/../App/Model/Session.php";
    include_once __DIR__ . "/../App/Model/User.php";
    include_once __DIR__ . "/../App/Model/LogAccess.php";

    class Container{
        private static ?Database $db = null;
        private static ?TokenService $tokenService = null;
        private static ?Session $session = null;
        private static ?User $user = null;
        private static ?LogAccess $logAccess = null;

        public static function getDatabase() : Database{
            if(self::$db == null){
                self::$db = new Database();
            }
            return self::$db;
        }
        public static function getTokenService():TokenService{
            if(self::$tokenService == null){
                self::$tokenService = new TokenService(self::getDatabase() , self::getSession());
            }
            return self::$tokenService;
        }
        public static function getSession() : Session{
            if(self::$session == null){
                self::$session = new Session(self::getDatabase(), self::getLogAccess());
            }
            return self::$session;
        }
        public static function getUser() : User{
            if(self::$user == null){
                self::$user = new User(self::getDatabase());
            }
            return self::$user;
        }

        public static function getLogAccess() : LogAccess{
            if(self::$logAccess == null){
                self::$logAccess = new LogAccess(self::getDatabase());
            }
            return self::$logAccess;
        }
    }
    ?>
