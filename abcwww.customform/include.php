<?
Bitrix\Main\Loader::registerAutoloadClasses(
// имя модуля
    "abcwww.customform",
    [
        "abcwww\\customform\\Events" => "lib/Events.php",
        "abcwww\\customform\\AntiSpam" => "lib/AntiSpam.php",
        "abcwww\\customform\\Validate" => "lib/validators/Validate.php",
    ]
);
?>