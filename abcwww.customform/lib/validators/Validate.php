<?

namespace abcwww\customform;

class Validate
{
    public static function validateField($field, $value)
    {
        $method = 'validate' . ucfirst(strtolower($field));
        if (method_exists('abcwww\customform\Validate', $method)) {
            return self::$method($value);
        }
        return true;
    }

    private static function validateEmail(string $email = '')
    {
        if (!preg_match("/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i", $email)) {
            return false;
        }
        return true;
    }

    private static function validatePhone(string $phone = '')
    {
        if (!preg_match('/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/', self::clearCharPhone($phone))) {
            return false;
        }
        return true;
    }

    private static function clearCharPhone($phone)
    {
        return preg_replace('/[\(\) -]/', '', trim($phone));
    }

    private static function validateAgree($agree = null)
    {
        if (!$agree || $agree !== 'Y') {
            return false;
        }
        return true;
    }

    private static function validatePolitics($politics = null)
    {
        if (!$politics || $politics !== 'Y') {
            return false;
        }
        return true;
    }
}