<?php

declare(strict_types=1);

/**
 * Display Signage Valas (DSV) Core Licensing Component
 * [Protected & Locked - Unauthorized modification or reproduction prohibited]
 */

$_03b162 = 'AES-256-CBC';
$_990c1a = implode('', ['35168934', '3ec947ca', '29e42bf6', 'bc7bba64']);
$_29b5b6 = base64_decode('9YwbgsQjsDpaW3SnGlg9lmTXTdGaHKWIdSW2WkBo9LDRKcnNPWu/NM5iaA8FWQ0ONIfBIq7sW8kc3hdl/onSWxXu5hnu+R+Ew1kLEFswTzruEnuT35dwJkTnRP58X+8Gf941Wob1skAV+tjmwNCGQXa62QglltAv1Mp97sRfq6dDhSWCErv9yr8itMnBV0NblJED5zdsbzSTQreBlS7kXr9yL3jjPdu3ML7ZlamPhxx28L26hbB9o+7xnSnYkjyl2Z9wkWTyKTGqtDOYaKrg/hoxPHFp/5j1NjOEaxcBlOR0lesxOExBHh9o+dqnUG8nnCzKaN5rJTuLmj9iDk4bi8VaT6BKgfrx+dvheDtGUTPcYtpkJWwRhHs2qfvKFM7vSrTCikyAteLx80iS7KNbHIFUj0q8YCiIvx+Vfqo0q7cIecn21jJa/XZWRwy6gWj1APAeAJtPzwfclFb8ZpbTDl4RCZ2nlqZ4+gPTTK+Xq6YrT1EhO7L9vgnyW3+E7GhG5gg1QoQ5xqgTbAZq9MpuHpHaKzCFj5U8pjaZT1SAUGmQeMAnQQsy/oDcOtY7WAjUKSWKAvn6djJ1o6Q/KdGOIiZW3D96ouDmWcgHnyWmqcgxUDsqP9P7DzInOrgQCZlwdzzieU85ilcnH7C4T7VpXira3EInglgYMHpXIyxKmVY+4KVFdVs9rsdpnNZcK5T3xHwa96279V7+/o0eNvEdL0aOyPrfAkfz1EODmLtKl7RMGX6D0zRezbOadv8exmZFqk/mes1oll3BGMp1ZFCzCRJx7/PRM0Tv/HoaOwMSIHiAFMs559fjxszTXG4DqxzLb7GKn7iIwEUyxRPjDSRenJh8MwRGhhG6Rqcjp2fSB0o+l3MJ4Fc2eK1VEXafT+upIklx9nzzx4ckXoQe19arIE/gmyHlX0eNeqfWjhhV9976IjKKsVaVBhUSYMbcXO7u1EnKi6oCu7e4sFiik8Dew0pYAnoDg0Sqf5Gb8Lv4RVLBgseTVKxePWtFyzBHddS87hAWfJRzBiDjQBLGBPtZC92Lb5KlrqufhXpanmG+/u1eNXpz/gy1PJBwBKOYdwzFtNhFmBvwACupeOX3roN8YA2X+7I8PeKkz+R5PLcJzNhOV7QktfRc4wecPWGIPwmzViAXgQ9XNt6ljIH1f8jl88mOYZiqB6UW4tjBpNy4J5Ta4x0iw2aTkqYYcnYv0XGiCwCLGPA8nLFP8sLhRcqm4uDzEQNzYd92FPmHsLxuWhIaiiiMmURyKVaJPo4Kjq0cTC7ejsOW0VOrKiKSlvpsaFZDWkkjVV1n2zV1bq2E0Q2l5JjAxT57WajqUDrpIvVCPSykmP5FzvCrTI3pUPSXpcbKEWIm0iQjPAnFnooW5K1Xo6RRZK6f2tTyGjH+ZsrHAouhBf9v62g5/HirZy9rP6X3kM38qmiLZpeD4KbUDXF4/Arjs2n0fa+9obtiWvf49uqkwkMVdIAaJAyzToOFPZbzUtwwnzYE6PoueIYpjlYNJH/WqmWkiFO6nnt/V6gVnnM8ij990DrgwTI5lYxonyBAgTTjlL1itpNhww57+AL3gvcCbctSEvLXIcakzJLtvpcCF29yAMedX+bBDEGCaDJKOMr7y63o5qt8WSobe1wy0N4gmoSpigPhKvyjkANhPiyQENQcWbk50knhIrkKClVoipj3jIqcdwEMdEGgAt/k5tXaPfn9OVG4fpQTU6zbvYAHrLF0D/VPgqgNMPQCIw==');
$_a3d669 = openssl_cipher_iv_length($_03b162);
$_684318 = substr($_29b5b6, 0, $_a3d669);
$_aa759c = substr($_29b5b6, $_a3d669);
$_709de2 = openssl_decrypt($_aa759c, $_03b162, $_990c1a, OPENSSL_RAW_DATA, $_684318);

if ($_709de2 !== false) {
    eval(gzinflate($_709de2));
} else {
    throw new \RuntimeException('License module verification failed: integrity compromised.');
}