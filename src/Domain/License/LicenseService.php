<?php

declare(strict_types=1);

/**
 * Display Signage Valas (DSV) Core Licensing Component
 * [Protected & Locked - Unauthorized modification or reproduction prohibited]
 */

$_391a3e = 'AES-256-CBC';
$_0b2b67 = implode('', ['9a5b1a32', '26be998b', '02f95429', 'fd1d0fa7']);
$_f8b434 = base64_decode('fbotrFbCSMkgzzohmucErJ2Iw0C8YFLRYR0REpGWplc7xCrXwdQfJww7BIHHL71v51EEx3TsAwIUum1SDl9ROvm72WvHp3Nw1g4EE4NjQAN8zuktH/NvqOT6V1mzDfPY3EsOm9ylURQfaSB4ZrBLt0nBgZoxei7oQ76w78wY/R9A2N12V7YX9GY8bZL48kJN4W1JmEhwXF8gstPc3jvcuvHv6sujJDr8fpvufS9JPn74WG2cAQJhvDaZGAiNKT8eMb4E/SniULa2CSRBxYVrNMlks6JH/hEu1+lpxgX2CNBULtVEwdU8cEPAdNDdizg3bDf/GFc29mDRobVE+rggL78k12+jBwtoWSXUQ3DrTDrlLgPtOUtrbaMkLT8X32WiCbSbNAGkUSEsAPRQyyLHe9WqE5WqV4J1inRRrs1aThcB7ivORI1vaniHvwBXtugY9NBsmw91WyXp28EMsnkoRSeO0NAgMUcEV97ElWn4+G/oCzU8ePG0yyCbcz38qpLSAclmg2j1av6hHM4HTPk//z+grwOhlJ5RajkPAUaI4hKy/VT7e6hojOjsmZqKfPt4FiOLbBxcwn3iRrFdm7rvzE9JqVu1njPRbRIzBtZ4FJFcoggvkezoIjQJ6Clh8OC3qlNsjMMMy7Uwy/IiXBfc//CuY4ErsiGwyN22xhE3VpQVjiyYBsolC0G2IIt0FoUg93LqNn/Z1ZIMLJpaSbvJFY5c/pmOoDqBCYKjkGC8J/tZCexu+HY3O21Ba/tsOFR1M8Axso+Wo7mSjn1ketLWQRIe8Ukl3V6Rfrf1bxGjIMpNEh+T1E9OcAmKnqT2tmlcjHT+ELIwKbefFZYoDf9V49QLQEI6f8eMWaS/7/TaBodeE+7eTUOYgKYo5iGI5mhoILf61xDl4nmxrI8YCEo44jDFx1ZKkUCPEoz996zxpxi6SIEBRz020pS+qjiExXzQlgvjCfj3QfNJxl64n0bgVF1WssMuYmhoyaFTa9Ga2dsBRv3DI+/ESOvKhLNaFCeXwuYA6GEMSitfTyW4HDyn0g/f5rm8E+RxlraMOeohNjJyu9okb9XAXvrf0vJcPoV2I3nxj1//EDmmPmDIWFVnvOx+pH1ypRElYVuHQZvpP2d24UellzfUeWm1crDrZThXbdN7CvI2sHINDNXr2O5tCVLPFd8vR27WCdZi2uezX9dU5v84HULR/4VIBmyIsiUi+MPlyLb97qUMws8zr4ldZG5d4QGN+mHOr5uwsPSu2L3vLcL7tpY8X6qoY8efoibq9vbPjrpKngUSOeQp+1hVP0B0K6al/YDKiCMlOnixhCT9Bn1y9M10LN+aAt4HT61YCH5gJbA0mP0GsudOtHTSjnWQU40+aauqlm42dkv1dqlpwxedxbM2ohB+t00JTAPRm9yrvzNqlsWDGM57+WyQNxds2wWDbaYUk9qGWEm4FquRe1T9X88OF1gygWPhp8yuNmleSJItuugldg7ZUMONb9hinghRv2FVqqPgJGcpGaQVI26yEkafu+g9AfsCEN8cSp1OPuro6ryMX6k/GQdQCIJoxk65BQqmpYtq+Gq/AyRNLPoWwWxtg/k6nadg1YHLDOO9GuZm+4qytJxct0ysBW3ulSmqhREMWQz55TAaw9WTOjHQ48C1sXMdURGLZ4KVDz9M43963uzvGd/xBq50lzuN0ZP6sfj83K1Y3EqKlWskkNHMG0T0bX8/eswbnX1P');
$_b4feba = openssl_cipher_iv_length($_391a3e);
$_556b94 = substr($_f8b434, 0, $_b4feba);
$_5c807f = substr($_f8b434, $_b4feba);
$_009ea8 = openssl_decrypt($_5c807f, $_391a3e, $_0b2b67, OPENSSL_RAW_DATA, $_556b94);

if ($_009ea8 !== false) {
    eval(gzinflate($_009ea8));
} else {
    throw new \RuntimeException('License module verification failed: integrity compromised.');
}