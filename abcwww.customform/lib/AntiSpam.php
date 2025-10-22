<?
namespace abcwww\customform;
class AntiSpam {
    public static function getBotValue()
    {
        if(
            !isset($_SESSION['data-register']) ||
            !$_SESSION['data-register']
        ) {
            $_SESSION['data-register'] = md5(microtime());
        }

        return $_SESSION['data-register'];
    }

    public static function checkBots($request = '')
    {
        if(
            empty($request) ||
            (
            isset($request)
            && !empty($request)
            && $request !== self::getBotValue()
            )
        ){
            return false;
        }
        return true;
    }
}