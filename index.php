<?php
/*
 * umo-v26.php — страница сравнения видеокарт.
 * Данные берутся из MySQL (таблица gpus, заполняется скриптом import_to_mysql.php из bd.csv).
 * Шаблон строки таблицы — в gpu-row.php, подключение к базе — в config.php.
 */
require __DIR__ . '/config.php';
require __DIR__ . '/gpu-row.php';

// Все видеокарты из базы: сначала новые (по дате релиза), ретро — в конце.
// Достаём карты вместе со счётчиком популярности (сколько раз выбирали в сравнение).
$gpus = db()->query('SELECT g.*, COALESCE(p.hits, 0) AS popularity FROM gpus g LEFT JOIN gpu_popularity p ON p.gpu_id = g.id ORDER BY release_date IS NULL, release_date DESC, name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Сравнение видеокарт у Мо</title>
<link rel="icon" type="image/png" sizes="32x32" href="data:image/png;base64,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">
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A//www.w3.org/2000/svg%27%20viewBox%3D%270%200%2032%2032%27%3E%3Cdefs%3E%3ClinearGradient%20id%3D%27g%27%20x1%3D%270%27%20y1%3D%270%27%20x2%3D%271%27%20y2%3D%271%27%3E%3Cstop%20offset%3D%270%27%20stop-color%3D%27%2342d9f5%27/%3E%3Cstop%20offset%3D%271%27%20stop-color%3D%27%231688d4%27/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect%20width%3D%2732%27%20height%3D%2732%27%20rx%3D%279%27%20fill%3D%27url%28%23g%29%27/%3E%3Ctext%20x%3D%2716%27%20y%3D%2721.2%27%20text-anchor%3D%27middle%27%20font-family%3D%27Arial%2CHelvetica%2Csans-serif%27%20font-weight%3D%27900%27%20font-size%3D%2713.5%27%20fill%3D%27%2303111d%27%3EuMo%3C/text%3E%3C/svg%3E">
<link rel="apple-touch-icon" sizes="180x180" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAAC0CAYAAAA9zQYyAAAsPklEQVR42u2deXRc9ZXnv/f3e7WXpJK8s9lgNssOBDAQQoxss4SGmA4h0nQ6zZBOh8npQ/ckhB7OhO5p2clMTne6+3TPnM6c7iYkIen0BAuHsCUEDLJDwhY7mMU2EAJmMfKqpVT7e7/fnT/eq1LtKkklWbJ/7xxZfqqqV2/5vPvu/d77uz/CbFmYqRsQh7aBtq8jp/ilrn62xMlYmsumOoVSKwE6g8HLACxmrReAEQJRG5i9bRX+8TZd7fuK/8sl6+Xv4ZJ1rv16tZVqm+bKdS7/Y/kq19uxyl0r/xDX+W6QgGY1QkAaQhwG6AAY+5jwFrHcLYKhPVE7+E6167LwMLivGxpEPBswolkAsujatk1sX7eucLIu2rHD1+pfcZ4CLtfgS4nxYYZeKixfhCy/+yatoJUCKwVoBms1szCP8VoX5vrATyPMRR+sC7N3DkhIgAgQEiQkSAj3NduGtu0kgHcgxC5mPC8k/4p++87LO7+42h6Du9/avm2txibSJyTQ3ZtZ9nWDQe4J6Hqbg5zKrgPrDdD6Sgh5towEwRrQuRw4mwUrxUzQ3lUggABmco+CyFjmCVpmFH0/s/tfIgYzmMDEADMEpCThC4B8fhARVDoJVs4bJOSTDHq4Tcb7t687PQMA6GXRvRLU10PqhAC6m1n20djBXrE3+yHWzmeh+SYZCJ5JloTK5KAzaTDBAQBiFmAQiKiqZTRuxtRgrrl/RRtlMDO0Z0ksEQiC/CFox4bOZd8koi0K4oevXN3xSonRmmGwaYYtcsHXumJ35jpmdRuYr7GiEUuns1CZtAaRZoYgIlGVDAPzDMNc/aQzs3YBhxD+oBDBMFQi7oDocSLxrV3XzPtpPjZCX59AT486PoBmpu4+iPydumZv8ibS4i+E3/8RgKCSo2CwQwzBIFGXXAPzrIC5MnZw4SaCJcNRMAM6l32Omf7+5Wvnb6lm0OYk0F39/VY+2Fuza3Q9+a1eGQhewY4DJ5XQALFriUHMEzyJBubZAXPpTjGDNWsmGYoKEha0nfkF2/aml69f8lQ5E3MH6N5e0btxIzYR6TUvHD2VWsJfJyFuIWHBSYwqgIkKYfSUT6KBeXbAXLo7zJoJLINRCa3ASt3rpLP/Y/eNp7wHZoGNG4FNm/SsB7o46Ot6NXErC/kNGQrOd0ZGNANMIDltJ9HAPDtgLvW1FQEko+1CZZJHWKm7Xrl+8d3TFTQ2Feiufra2ryPnI88cOTkQi3xLhoK/7yRT0I7tEMiaqZNoYJ4FMJeta9aOkD5LhKLQ2eSDFB+67aWec/Y32wVpDtBFgV/XruFrEQh+WwYDJ+dGRhwCPMXewHyiwly4DszMgLJaYpbKpPdTLv2Fl3//tMeaGTCKZvjLANDXQ+qKV+J3USjyMzC7MBNZBmYDc+FvRERElhMfdohxMgWjP1v58P67Cm6Hx9Kxs9DMAkS6q7/f4oWX3O2Lhj+XG45rZgYRiZn01QzMsxzm8vcwayaCbOkQKjHyvXk79t66fdM6J8/UjFvo7s2bJYj0eT/fFcGCSx7zYLbBTAZmA3NdmAEwkQAzOfFBW0ZbP3f04nMfO+/7uyIg0ti8Wc6ohe7evFn29fSorucPLuaWloetYGh1Lp53MWY2ijYwz0GYy7bLWjtWS7ulM6kdKpXasKfn9APYvFlOJrs4caC9R8KaHYeWiHB0qwyGOg3MBubJwlxY1dqxou2WyiT3OMODV712c+fAZNyPibkcrtPO5/18IEKh6MMyGOq04yO2gdnAPBWYwQCRsOzEsC2CkU7Z1vHwed/fFQHAEw0UrQlKcwSADp7U8pAVCl3kWWafgdnAPBWY87+IyOeMDjlWtP0i1brwoe4+XIOVK9HHTI1Keg3T37UNsq+H1MFXRu/xtUbWGzfDwNxMmAs+MAnLGR1yZEts/R7f+/f09fSorm1oOEhsCOh8BnDNruGv+tpabskNx42bYWBuOsz5FRLCcuJHbdnScUvn/e98dfs6crr6+xvyJsYNCvP59o/9ZvgqXzTyhJPNOFBKgvIjRAzMBubmwTy2c8wgqSgQtFQqcfXeTy/d2kjtR32ge1lgI7hrd3IRCfEigxapXJaNzmxgnl6Y8/vAmnwBAuuDLDMX7Ll+2UFsBNUbt1jX5ehaCwEi1krfLUKhxSqXVQZmA/OMwOw51DqXVSIYWYyMvBtE3LV2m5iUhc6XgV6xa+TzVqz1ntywFwQamA3MMwFz8WeYHau1w3JGBv9kb/dp36nnelBNVwPAmv+UXASNPUTUyo4DcJlFNzAbmKcZZm/RJC2w5rjmXOdrL3/3ILAR1VyPqua7e6Xnp+Tsb1rRSEzncmxgNjAfI5gBQGg7xyIcjUHxN7Fpk+5eWd0YUy1V4/Jd8Y/5wsGnnXRaEZfpgAZmA/PMwVx8HZQIhCSnU2v29Cz9JTazRJnrUWGhO7vBYCaC/iZIVB6tgdnAfGxgdldcTeKbYCZ0V35ClAeCm4j0mpeHbvC1tl3mJBLKjAE0MM8OmAEwSZVKKBltu2zF/e/eACLdvZllTaA7N4J7mQVr8dfadrjEITEwG5iPKcyej0wEVg5D678Gs+jbvZGr+tB5mW7NruGPy0jLY85oXJMwrQYMzLMH5rF1rSnUKlQyce3rf3Daz4t96YKF7szvnubbQWAIYgOzgXnWwQyAQew2KdK3u9Z47F1e80O3kPqKnUdWcCD4MhwtvS2TgdnAPKtgLu4QLIRStn3ebz9z+t48wwIAura5llpL3y1WJGIxtAIMzAbmWQkzABCzVjLcagmSt7gMuylxcpsZEV/7+huBRGLhazIYXKazGW0aJxqYZynM3gdYky8oOJfZ5xzMnfvml87OAiDRzRBgIJlauNaKRpfpTFrDwGxgntUwAwAJzmW0CEWXyUXBtQDQvZmFOLTN9aOZ6JNkSWYizQZmA/OshrkQHGpIHzP0JwHg0AKvq9FFO3b4QmL5XhEML1eZtDbNxg3Msx1m7xhctyOT/l109OiKnV9cbQsAiFjLVwl/cLnKpNnAbGCeGzADYBKcyzD5g8tTLfNWFXRoDbFGRoIAQRmYDcxzA+bCe5UIRaAlrSkAzRqXsW70RBuYDcyzA2ZXwCNAa7DSlwGA6GaWRDhf52yQZmFgNjDPGZhdQVqwnQVBnN+9mSV9dMfwciH5FZAVglaMQvbQwGxgnt0wu+uaISxi5aQ12R8SRLpT+IIhKMfAbGCeYzB7M4Uqh8kfDEktOwVYrCSfH0UztBqYDcxzBObCS5qkDwpipQDhDOMzG5jnMMzF7zlDEHA6tK7+nQZmA/NcgJkIrDUE4XQBwhKtHECDDMwG5rlpmZmgFZixxGKgA0oBxSPADcwG5rnjZgAM8hjuEGCES77FwGxgnkswF2+cERYgamOlMNZN1MBsYJ5DMHtN8LR2AEFtovBNBmYD85yEuWjRDMvAbGCeyzCXcyAMzAbm4wJm77+WgdnAPKdhLntdGJgNzMcLzAWgDcwG5uMBZjAgDMwG5uMF5hKXw8BsYJ7rMHMF0AZmA/MchrnUQhuYDcxzHGZwft4UA7OB+TiA2bXQBmYD83ECc6kObWA2MM9xmMd0aAOzgfk4gBkALAOz+4u1ztfU1rjQcIdAlL2HiPIzMzUHZmZ3Xyi/YygMvWCuRzkAIdw3n6AwgwHLwAyANWQ4CpK+caaxKzsoAthxoFJJeH0vpwwzWT7IUBjQJV0lyjbEVX4RVCoBr1f9CQkzA1756InsZngQxV/Yhtyh/SDLXx2ECpgJ7NjwdSxCy4VrwI7tQj0VmH1+5A7tR+KlZyAsfxXI6s8ZGb3gCljRGFg7da7l8QszKoA+EX1mZpAvgINb7kb8mZ+CgkVAlCxUuaoUZLQN53zrUfgXngzO5UreNhGfmbWGDIRweMu/4ehD/woKtYNV1fnZqy/MOPMfHoQvtgCczRXi/RMJ5lKgT+QAkBkiHAWF22FF2sBcA6TyvmrSgj18GEP9D2HJf74DTiYNknLiASAzRCCI7P63EX/hSciOU0BC1qgaqw2060Nzwe8+0WAGl93GJ7SaoTVYKbBW7u9qP7r0Rzs5CH8QQ9sfgjN8GGRZ7jdMUM1grSECYQw//QjsowdAQoCVU/F94/0UX4cTEWZXtjMwT35hhgiEkHn3TcRfeAoyHIVWesLSHEkLTnwQQ9sfhAiEwHqSO8f1rtPxDzPAeR3a6MyTB5xBQuDo1i3QuRyobL6l8WBmpSBDUYzu2IbMvjcgAiGAdRNuthMPZgAQxjJPkRutIUIRJF/9NZJ7dkKEIoDWDSdNyFNLBp+8H5T3gafK8AkKM0pD4RM0A8hTZ5yEgM5lMPjkj0FSgpkbghlaQwQjSL7+GyRffQEiFHGTKs1eThCYmauM+j7R0tlNcaWVhghHEX9+K7LvvQXhDxbN4Vs7nc3MgGVh6MkfQ2fTnoVusqvRDJiZ3UQPa/fpo7WbwNHKzWzWOpkzDLMn200TzKw8a0PjwMwgWZmyrQcza+VZQapzkhhM5EJSp+6CmuKHMITlgz14CEO/eASLP/tlONkMIEXt2gxmUF6qe34rRDgKVnp2wMwuvN78loCwQFK6UmJx8khraNaA48BtlqhBIEC4ExTPNMz1degpwawh/EGIQgq3HqgMlUyAHaeQPh7PMstoG0harhpA1YI7BoigbRs6lahIS49baDS+k1Gxg6xdxWNo+0OY/4mbAcuqOPZyqU4Gwhj+xSOwBw/CauuonkghmpAezVOBWbMLpeWDCLQAJKCzGajkMNToMHRqFDqbdi2zkCB/ECLcChmNQUbaIANBsFLue5QNJiq6AaYf5lKgmwSz1goyGEJi928w9MyTEIFgxbcy56+T68TO//in4V+wGGx7J6EOzBCEQw9+H/bhAaAsTV0MAeeyCJ66HO3rNnjqAzUHZubCDVNGtCvhvfMG4jv60b7uRjjx4UKipYIty4IzchTDv3iovlSnlJcwafxWc8tAJgYzKwXhD0IGQrAHDyK562mk9v4amX17YR8dgE6OQOeyBTcDRICQruGKtMI3/yQElq5AeMUlCJ11IWRsPnQmCZ3LupZ9BmAuKU5qms/sPUpH97yID+75OhBoB+qlcJVC2+o1CCw+DTqXQ0WKucxnJhI49JPvI/3ar4FAFCh+THuGk6QEZ4fR9tEN6LjmJiCbBUBNgNlNgEAI1/JXgOZKeINbtyD2sU+4j94qMLNSsKIxDD37Y2TeeQNWtM1LjFTemLIlBpUYqbyBat5v5SV648DMCoCA1dKO7P7fYXjrfYi/8DjsQ++DlQOyfO6PkG7xluUbO9EMsGPDGToE+/B+JF95BkOP3Qv/oqWIXvJxtK3rhm/hUujkSNHTZvpgLljopgeAzBC+ACg4D1ZbrG5NAivlug9ehq3kupUHgN61sqJtEC3zPZdGV1EdJJyEBRlpcW8wNMEyE4FtB9bCDkTPuwxHHvkBRCRa8v1jEt4LSL7+IiLnXgidTrp+aNE+uFJdFkNPbXGzghWZFgJn0wguW4HQmasw+PMfQURaqh7rRBMrJSBoB+Rzn6CH7/9nDD76PTgjhyGCEbcUAFQwUoULUO4PkgBZAuTzF95vH/kARx/4v4hv34L2T3wBsav/yPWxczlAiGmDuVSHbraaweymb5Ua/8c7YTSemlEARzWwbcercR4/ii+LEOvpc9DZNNrX3QCrfZ5bYVf2IRLue4ae/DHI8hVcicJXKg0RDCP52otI7v71mG5dvo1MCq2XXwdr3hKw7blM4/rPjbsZrB1QIAJn5Cje+5tbcejfvwm2M7Ba54Esn1sKoJWnqdfTg7wTXPR+8vlhtXZApxM4dO/XMfC//xw6nQQFgtD5J9E0wDymQ0+jNDe5aL0BaW5SwT9XVVsa3rQg6GwagVPPROvF66FTSU+hKZPwQp6E9/7bEIHgmC7NAEODLAtDT/0YOpupKtWx40C2tiN2xQZwNj3mukxW7ahimUUgDPvQe3j3f34OyVeegRWbDwgJVs7EiqJqXD9WDiAtWG3zkdixFfv/7lY48SFX0qz2pGkCzIwqo76PDcw0LTDnFSgazzJP0PWA0ui46tMQfn+VRAhD+Hywjx7A8C8fBQXDhScFPPUn+/7biL+wFSIcqZDqSEiodBItF3QhuPQc6Ey64LI0BWbWICsANTqM9//+NuQG3oZsaR8fZBJucCeEJ+V56/X2zQNbts5D5q1XMPDPXwbbrttRPmtEM2AGl436bmrSZBIsj3NMk4OQasM8qfuEBFQ6iUjnaoQ7V7s+cpmVHZPwHoSKD4KkV4WnNUQwhOGnH4EzeBjC8lV698wgy0Js/U2uXEFTNpYV7gFZPhz4zteQeWcvZKTNhbneCRQCnE3BGR2CTo2Csyno1CjU6BA4lwbKVYyKOMmGbOlAeu/zOPKjv4MIRsbqVZoIc5lsN10ZwAaIIxrzxYiaB3M+8cKo0KsZU9i21iB/AB1XfgqJl571gqFqEt7rGN25HbErboAzOgyyLDjDg55UF6y07kJAp5MIn30BIisvgZNJTkiya0Sak9EYRrY/gPgzP4XV2lEfZk8D18lRBJefh+iF6+E/eTlEIASdSSH7/m+R2LEV2Xf2QoRb6l54VjZkawfi2/oQuehKRD70MajkaOnNMEWYi4A+VjDnOaOiVHF9mCe2eR5njOAkbxThjt9rvfhKBE9djtzB/SC/v/zZDiKBoSe3oO3y33PHLQajrlT37m+rSnUEAjsOYutuBAXyCg5N6HDrZQBJWNDJOI4+fI/nLnH9K+MFegv+6L+j/erPQgTDrmLlGZ7oZdeh/fo/wdAj38bgg/8C8gfHvdoMYOjRexBe8ZFSd6UJMHtB4TTXZjTgk+YDeKbxYJ5i9cW4rQao4duQHRtW+wLErtgAnU1VBHd5CS/x6vNIvf4SRCgKncvUlep0LovASUvRcslV0OmEW4rKEzidNDYwveLJqxVEOILEi9tcixoI1y9TJTcAXnjzVzH/xttcvXlkEDo5CpUahZMcgYoPAlpj/h/8N8z71J9BpxP13Q+tIIIRZN7YidTrO0DB8srEqbd8EOAGAosJwTwJ6EiU6Mx1YebpgnmisaErz8W6NsCKza8t4WVSGHpyC6xoDMm9O5HcM55Udz188xaBbRsTdqDrSpQEaEb8+ccbuBwSOjWKlkuuQezqz8IePOD+XVpg4frURBIkLIAZauQw2jf8F4RXXApOJ+oHikRgO4fkzq1uFpW5aTAXdGieDjeDGyajgXw/l5nwCbrRjTSBmSg+RNCZNIKnnIXWi9dDpZM1rHQU8eceR+7wBxj51aPQmXQdqS6G2JoboLOZhjODDUmU7CkvgweRefMlkD8ErmOd8/UcsWs+CyjHLTjKJ1nKry0RWDNIWGi76g/d7dbbddYgXwCZN3ZCJ+KAsJoGc6kO3Uw3YxJZuGq+bkWyYBosM2MqsiuBtUL7lTdB+AKVkHiKhZMYxoEf/oPbniAYri3VXdiFQF6qm3QwWOVJxgxYAWQ/eGts7GOtgyYBzmUQOPUsBE9f5e2LrFs15yaTUgiefRF88092pblaN6SnsthH9sM+/D7I5yu4Ps3oLCWOLcx5u0jjwzxZLboiA1nlLEySGRKuhBfuXI3wiouqSnjwBsAObe2DM3TYzcLVkurW3eQqKFPW6lCxfQgL9gdve1lHUce2uC5BYGknRCjqpqzrnSuvpRQrB1bbPPhPOQtsZ+s/YaSEziRhH36vcHM1q02aOKaWucAzTQvMGM/NmLrxd90KXwDt6z8F1qoGjK5VqnqRhYDOJBE+68OIdF5SclPwhH386iNN2DvF9tGBitE0tayAf/FSEIn6sUtxKzVmQFrwLTwVqHkeivxApaCGDrhPhGbAzLWCwmaNNJmgcNcwzDx7YM4/bl0Jbz0CpyyHztXwf2t8YUGqW3uj644UJRwmfLB1hk0xM1RiuKGaEBBBts4D54v8G61nZkC2zmvwirv7w0x1Y7iGG1h6a2JaYObJEzKxLqBNgnmy3OT3OS/hrdkAnUk1PpSqTKpT6WRBqptq7FB8yOQdNOeyDXzObVhDXg0KTbQ/cyDUcHitvf2hJljmwqjvYwpzcYawIbXFtTRuOSo1dlWbbJmrHWxecoutqS3h1Zb+Umj96PXwdSxyg6liNWGyu1ZL/pzIwdN4hqfWCaXJmIbJw1z2gjj2ltnrWFTPF2fPRyMC2/ZYIqOhCzSOZeYpmj/vIupsBoFTl6NldXUJr+pmlAPZEkPsihsKrkplm7Im3HterEI+f8M6J1cbFNFAcb7OZRreabICVTczWZgLPvQxg9kLqtjJlfZDruFmkJRQiRGo+DAg5cSCt2b54jUtC4G1Rvv6T1WX8CqkrrGqusBp+ao6mtL+1T5OBhFBRNvGDQrJMzJqdKhGsF4d5nwNmBodavDsEWSkraIOfiowA4CYnlYDDV4QcuUelc3UjHa5TL/MDbwLZ8SrYGvwEdpQFD1ZgvK/8oVFK1YjfO6F0OMUFpVLdc0LfGvr7VbH4saCQhDsg++OnbiGhk2519I+9B5AcpxDcP10GVtYkn6fKsylPnTTLLMLRyOPXCICHAdqdMTTRmvXyLLWIOnD6MvPQecyDQde1XXoKhaBpwBz0T4KfwCx9Te5Q8tq+ZNlUp1KF41PnBaJ0lVS/IuWuaNo6lXFeYYj885ecCZZ0KzHHTYlLajEMHL733Rdm3pPKE+b9y04tdBXe6ow5y+haK5lHuvrIILh2intMgudO7jfzf9zDTUjn3GLD2L4Fz/1RknrKVswrmzQMWmY3USLdCW81etcCc/OeAXxVPJD5HYXja29ERQMlUl1TTDW5R2NvGSJ/+QzIFs7AGXXyeRpkD+I3HuvI/veb0vT5DVg1lq55bJvvQzn0LsgX6BOJtIr7OpYAmvBKWAnB67WX2WClnnMh24mzIWYQkO2tRcGv9Y98UIisXuHB7+uArMG2zastg4c/sn3kH7ndYhgaOJyRSOBx2Q3V3xuHBtWbD5iH/sEdHLULcF0bLDjuD9KQaUS8C86DdGLr4JKJ2o3w5lUwMqoaM8FAjs5WPOWILhsZW2tvPAEkdDZFIb77wP5A26Z63hjAIkQ33bf+LUcJMB2BsHl50NGY9XrsSdhmSva6TYF5sId6MA3b7ELXh1LylpBhlsw8vxTGHnuCfjmL3GDxHy/Y+2OCPctWIzBJ7bgwI++BRlpnXKHoemCOV8DrTNptK7ZgMCpZ0GEorBaO2C1xryfDohACLF1n4KvfYH7yK1SdMVT3LEKrZwZJC1EV181fnpdK4hwK0Z/+SBGn/sZrNgiwOtX7Rodtx2Y9q6RFVuIeP99SL64DSI03uh0BoRE+IIrXfin6GaUv2Q1E+Z89Mq2Df+CJfB1LHTnLfH56zyC3F/7/vYOnBKPI3b5xyGDoYK1s48cxIH/9y0cvP/fXLdkgp2ECjtG4wQek5XvqMxcEUE7OfjmL8Hp37ivpCPUGFwaMtziWmeSdYLYyTn21T5OXtAavehK+JecDvvoQP3rAgDSwqFv/yXYyaHlI9cXpEZot82a9EaHjzzxAxz5j7/xWgHXH5fImRQCy1YhtOLSytqXKcJcAnTTGieCoB0HvlgHwmeuROb9t2D5g3WmeXCDEJVOYt/ffgmhM1cidNrZIClhDx5C6q09sI8cgIy0Th7mJlnmuma6bEYqKAUZahlLHqG4NoMKYIyryDRD7fBUCCgbsm0+Yh+/GYe+u9GNRWoNwfIsOjs2Dv7LnRh99lFEL7oK/kXLgEAQnE0jN/A2Er9+DKlXfwXhD1UOfq0RM7VdcwsoEAYnR8YGBDQBZoC9RjPN7gLKDJBA26XrMdj/0PgBlxf0kc+H9FuvIfX6ywWrQoEgrNb2kqE/0HrC9cJNt8w1YS7yW4thqdD7aRyYJ66+1EwG8pi6olIjaFvXg8SOJ5Da/VxtPza/Q1KCZAjJF59C8jdbQf6QC7py3BYLUkIEo+M7/dKCHh1E5JLrEFn9cehUvOkwgwExHS1tSQqoVBJtl65H8NTljRWse81KRCAEq8X1N2WkxTt5Y6luduzxH5WNwjzlHEuNeQDHWiR5x02um1y03hDME1Vf6ioy3u3kzX+46PNfg9Wx2NXLpVX/ujBDhFsgIjG3HRgAkj6IaMwtMWU9PsypUfhPPhPzPvNVoLg0oIkwl+nQzezP7PrRvvb5WPTpL7hdcxrN7LEem7gn37XHa4ur4kNY1P2nCC3vdDtcNpJeHmcSn8lnOWduUsupJVbKWtqSAOfS8C06DSd96f9AhFvBqVGv1QLqasfIT0xU0OvUuO3JSFrQyRFYHYux8E//CbKlY2wAQJNhLtKhm99sHFLASYxg/nWfQfvaDXAG88XtE1vy05s5I0ex6DP/FUtuuaMhi19wt+skVqZqmXk2wYwGYC6S5VRyFMEzzsfJd94D30lnQMWPeoZDNuHLx7bjxI8isGwVlnzlbviXnAHOZ1CnAWYvKJzezvns2Fh6xzehkqOIP/8UZGweSMja2qZHYz5DpVKjIMuPU277Ohbe+Hl3XQiQdJtwVwPb7ZRpVVrwehPBl2yzxh0CLlyoGZltys2pu/slZGUv7NpSjteSpN6wKQmVisN/6tk45a4f4Oj9/4T40w9A21mIYMTLIaCQGxgnQ+YN1PDOt7KhknFQIIzYtZ9H+w23gfzBsVHh0wSz2053GmHOS3jCH8TyTd/GB9/9Oxx+9IdQ6RGIQND1hYUo0kTZLQ91bKhsBgQg8qFLcdIf34noqkugRodAlh8qEYcePeomCKp2H7XA2aHSZuf1YCaCTiWgk0fd5Ee1mWRJuE28lSrtZzFdltmTADmbAqePwmGu3nK3JAZxJtQ5H0KCMymQP4iFn/8aWj66ASNb/wOpV38FNToIkAT5fK7fnM94VnTs4bHkkZ0DmCFb56HlwqvRuv4PEVh+PnQ64fXom16YXQs93XOaCOFV00mcetsmtK/7fRx9/H4kXnoWucMfQKcSnrX2VA2fH1ZsPsLnnI+OtTeg9ZIrQVLC8Vpqac2Y94mbYX/kaneApc4LZQSmMS2Yc1kETj2zUGNcD2a2c4itvRHhs84HfAEAunT4f37F65hE9aZea9IEPUwEzmUQ+XAXKNzizduiq18Hbxdl63z3phvnZit5SQhAOdCJYYTOugChsy9Cbv/vkNr9DNKv70Dug9/BGTni3liO4+1Dvh5VgCwLIhCGNe8k+E86E8FzLka48zJYi5eBve3me+FNN8wAQKv7D/G0wVxyI7vTHchQBGT54AwfRfbAe7CPHIBKxN0O8sEwfPMWwr/kNPjmLQaBoJLxwrS/+d2R4ahbPqprqRnspVhzrnhfPpNKxUBZdh+zls/bRpVnu1dUzMxQqdGqGnLTZ5tiBgVCEL5AbZiL1nU6CV02cX1dmMtdPq3dGNwfdLsgacct1x0+AhU/DJUcgc6k3bGTQoICYXcqitb5kG3z3XJQr50w59PrJKZFzaj1Hlrdf4hnYrap4rQqa1d3Fr6AN50weQUqnsKRy0HbNgA95rOW9Db2/G+mOtKcG/Pmq/LGnY9Ra68Ih1Crqp3z7mKV7N60TZ2W77ucn3+mRq8+sBuITxrm4l3yjI87AZAsii2ke50Ktet6bEppZQNKebUcomZt93TCDADWjMLsWU6S7snQ2RQ4zShKoBWampAgANUngieSZWNrq4x9q+czVzuJQoAg6iZNaDoDwFrnmGgsQAOqHyc1ebYpIhDkmIxqZ8E5riw7HbvDXddNEMCyQYm0+TCXzLEyIzBzWWQMAkT9vhnN6tcw2fkYZ0JnnvWTWhKNWeYqokrD12GaYebKUd8zBTMa6jVnYJ49M7RO+TrMAMylmUIDs4F5jsNcCrSB2cA8x2H2fGhjmQ3MxwfMJRbawGxgnuswF4A2MBuYjweYgeKJNw3MBuY5DvOYy2FgNjAfBzBzrXa6BmYD81yEuVK2MzAbmOcwzKVAG5gNzHMcZpTP9W1gNjDPZZhLpqQwMBuY5zrMrstRrcm2gdnAPAdhBhEEsx7xuuGzgdnAPCdhZmYICdZ6RDCQKmlla2A2MM81ywyv1zgoJYhp0BsizwZmA/NchBkEZpJgxqAA9ACKgTYwG5jnEswAmIlBEgQMCCa8TfkR1QZmA/OcgxljXQGAtwWUfstYZgPznIW5+IhYvyWIeLe27bJZZQ3MBua5BDMEHBtEtFsI5dujc9k0pEUo7kJoYDYwzw2YGVKStrNpG84ecUZi4T4G9gm35zIbmA3McwhmNySUfhCw7/CSvftEXw8pMF4inx9cPv2pgdnAPKthBsCkyfIDwEvo6VHCFaX52Yq2tAZmA/Osh9n7hwggPAt47XS1kE9TOgkq9N4yMBuY5wjMYKmzSTDrp93okADfW+++qnOZ34lAkJhZG5gNzHMEZg1fkHQu97slwfSrACC6nmJr5xdX2yDxBPlDzAxtYDYwz36YAQZp8gUZEE/s/OJqG739llh4ON9BVfxEOzahYhSLgdnAPBthdqFlxyGAfwIAWLmWRV8PaRCQ4pFtKjm6TwaCAszawGxgnuUwa7ICQqdH90WE3AYA6CEtAHDXU/3Wm9ednYWg+ygQhut2GJgNzLMVZgCApkAYJMR9b37p7Cx6+y0ALABg+7a1GgCUwr0qEXfI7XbNBmYD8yyFmQGSOp1wNDv3um90GS6a04EFiPT5j77/mIy0XeOk4hpcNveCgdnAfOxhBpgVhVqESsUfP/CVldeilwU2kUZxANjd5824IOgfGSBoJgOzgXnWwez+xZ1omsQ/usHgmGEuhbaXBTYC5z2y/9ciHL1AZZKa3JliDMwG5lkCMxT5Q4LTyRc/iHdeDGwENm0qSM0lnZO6V4JApJnoayR9VHUHDcwG5mMHszvVnfARE76GTaSxcmOJUaYqeyEA8HmPfvArEYpeptIJRe68VQZmA/OxhllRICp1ZvTZgds7L8dGUN53rmqhC740EUPjTmhdmLbYwGxgPqYw5+0va0DwnSDiYt+5JtB9PaS6N7N8+YaTf6nSyX+XLTGpWSkDs4H5mMKstRLhNqmziX8f+PKqX2IzS/SQGhdoAOjbDUZvr/D7nDudZHxYWH5C8UTEBmYD80zCzNBk+UlnRod9EHeit1dgd3UbXhVobCLdvXIj7fy9ZQOcy90hQi2iUPxvYDYwzyzMAFgjGBHQzh3vfqVzACs3VvjO9YH2XI+u/n7r1U+e9h1ndOgRqyVmsWbHwGxgnlGYmR0Kt1kqMfTIB7ev/A56+61qrsa4QANeSpyZNOVu1enUAeELSBTXSxuYDczTbZl9fqkzyQPKz7eCmfIp7kkBjU2ku/sg9nzi9AOcS99MlkUspB4bTGtgNjBPG8wMkhpCklKZmw//2aoD6IOo5Wo0BnSR6/HKJ5duVenEXbIlZjHIMTAbmKcPZoBBDoVbLc4k7zr0lfO2judqFAl7jS1d/WxtX0fOqp/s/55sa7/FGT7ikBCWgdnA3HSYWTsi2mHp5PC9A7ev+Bx62cImchrhtGGgwUzdfX0C6Mbe0MDjMtSy3hkdcoiEZWA2MDcV5nCbpTOjTw2cdO416OsDNndrEHEjmIqGgSbivt27ua8bWgwdukFnkjutaLvFzLaB2cDcHJjZFuE2S2eTO7X97g3ohkbnbm4U5olZ6LG9FiDS5/5gzxIr1rFVBCOdeUttYDYwT90yJ/fYidGrjvzV6oHiOudGFzFhoIk0Nm+Wr93cOaDT6StVJrXDirYXadQGZgPzJGHOpnY4OfvKI3+1egDdm+VEYZ4c0ADQ06OwebPc03P6ATk0sFalE09abfMsBttgZgOzgblRaY4BW0Q7LJ1NPanffXvt4TtXHUD3Zom+HjUZNAlTWTz3o6u33zpy0Tl3y3Dr51R8SDMzQCQMzAbmekkTBkFE2oROJb43MLT/Vmxa50zGzSherCkBTaTR2yu2b1yrQPTHKx9477cUCP0vKAWdyzpEZBmYDcwV72F24PNbJAR0auQvB7684hsAE3p7pwTz1C10iaQH0ddDqnPLu9eSL/Bt4Q+d7CSGHQIkiMjAbGCGZgZBUbjN0rn0fs5lvnDgjlWPuaWg0EDjakZzfehqkp6XUdxz02mP5TLDl+pM8kGrpd0iy0es2TEwn/AwOyR9ROGYxdnkg9mR0UsP3LHqsbEM4NRhbp6FLlq6N7Ps81KUnT/ZfyvB+gYFQ/NVYkgzwFTeGsHAfHzDzKwAkAjHBGdTR7RSdw3cfs7dAFCrSP/YW+iipa+HFHp7BZjFnk+efDfS8Qs5k7pX+ENCBqOSwQpFjdUNzMdtqwGdHwNIvpDgbPJezqUvHLj9nLvRywK9vaLZME+LhS5eunr7re2b1jkA0LnlvfUQVi/5g1dAKehMQjOIARLufhiYj4uORswaYKJARBBZ0HbmF+Q4m/Z/5dynAAC9/RY8JqZjmVag8wEj+lC4G1c+cPAmhv4LWIGPAIBOJ8CsHQKEB7eBee7BrMGsQWRRMAowg53ccwT6+/1/vnxLwb3ohp5IGnt2Ap1fNm+W6B4rMlnxwKHrWKvbwOoaGW6xdCYNzmU0AxoEASZhYJ7NMLNmkAazICsgKBCGTo86AD1Ogr61/8/O/OmYQesT6OlRM4HZzAFdJWgEgHMeOPAhofRnmfkm4Q+cCWlBZzPgXAYMOCACMQu3/VPR7W1gnkGYvXmGmTTcxrQW/EGQFQQrG2xn34SQWwTjh+//+fJXxoxY84O+WQd0ycHuBueF9KXffTsYjgbWadAGsHMlCetsEYoASkPbGbCTAyvFDGhiwG0YQq4FyGuH9S7ibIK5mk/F48QO0w8zw83wMrOrR3mvCwhJJP0gnw8gAZ1NQiv9hiD5JJgftlpk/zt/fHrG9ZFZYCVopkE+9kDnl14WXdgmthcFChf96w5fsv2k85j4cmJ9KWv+MIOXkuWPkPR5uqYCaw0o5Y4I046xzFOxzMILYUgAJN11AHAcsJNLgvAOSOwC6Hkt8KsBOfwyvrh6rHS4t98C1uqpZvrmPtBFwWN3H8ShBaDt60pHJ3T1s3Vk6NBSJ2d3MvNKEJ8B0DKwXgyNBcw6BEFt0Dy7YZ6dlhkgAms9Aog0gw6ToAPMvI8IbzHJ3dKy9rz3wWnvVIwa6e23sHItNyvL14zl/wM9nbhBGhQq6wAAAABJRU5ErkJggg==">
<script>
/* ===== ПЕРЕКЛЮЧАТЕЛИ ФУНКЦИЙ =====
   true  = функция включена, false = скрыта. Код функций НЕ удалён: чтобы вернуть, замените false на true.
   login      — кнопка «Войти / Зарегистрироваться»
   processors — раздел «Сравнение процессоров»
   rate       — раздел «Моя видеокарта»
   error      — раздел «Нашли ошибку?»
   contact    — раздел «Напишите нам»
   feedback   — кнопка «Отзыв о сайте»
   cookies    — уведомление о cookie (true = показывать)
   brandname  — крупная надпись «у Мо» рядом с логотипом (true = показывать) */
window.FEATURES={login:false,processors:false,rate:false,error:false,contact:false,feedback:false,cookies:true,brandname:false};
(function(){var off=Object.keys(FEATURES).filter(function(k){return !FEATURES[k]});
off.forEach(function(k){document.documentElement.classList.add('off-'+k)});
if(off.length){var s=document.createElement('style');s.textContent=off.map(function(k){return '[data-feature="'+k+'"]'}).join(',')+'{display:none!important}';document.head.appendChild(s);}})();
</script>
<link rel="stylesheet" href="assets/style.css">

<link rel="stylesheet" href="assets/anti-flicker.css">
</head><body>
<div class="app">
<header class="topbar"><button class="burger" id="menuToggle" type="button" aria-label="Меню" aria-expanded="false"><span></span><span></span><span></span></button><div class="brand"><div class="logo">uMo</div><div class="brand-text"><span class="bt-sub">сравнение<br>видеокарт</span><span class="bt-name" data-feature="brandname">у <span>Мо</span></span></div></div><div class="top-actions"><label class="theme-picker-label" for="themePicker" aria-label="Цветовая тема">Тема</label><select id="themePicker" class="theme-picker" aria-label="Выбрать тему"><option value="default">Океан</option><option value="mono">Чёрно-белая</option><option value="light">Светлая</option><option value="purple">Фиолетовая</option><option value="forest">Зелёная</option></select><button class="btn login-btn" id="loginButton" data-feature="login">Войти<span class="login-more"> / Зарегистрироваться</span></button></div></header>
<div class="drawer-backdrop" id="drawerBackdrop"></div><div class="layout">
<aside class="sidebar panel"><div class="section-label">РАЗДЕЛЫ</div><div class="navitem active" data-page="compare"><span class="ico">▦</span>Сравнение видеокарт</div><div class="navitem" data-page="processors" data-feature="processors"><span class="ico">▤</span>Сравнение процессоров</div><div class="navitem" data-page="rate" data-feature="rate"><span class="ico">◈</span>Моя видеокарта</div><div class="navitem" data-page="error" data-feature="error"><span class="ico">⚑</span>Нашли ошибку?</div><div class="navitem" data-page="contact" data-feature="contact"><span class="ico">✉</span>Напишите нам</div><div class="navitem" data-page="faq"><span class="ico">?</span>FAQ</div><div class="help-card"><div class="help-title">Помощь в подборе комплектующих</div><p class="help-sub">Напишите нам в удобный мессенджер</p><div class="help-links"><a class="help-link max" href="https://max.ru/" target="_blank" rel="noopener">MAX</a><a class="help-link tg" href="https://telegram.org/" target="_blank" rel="noopener">Telegram</a><a class="help-link wa" href="https://www.whatsapp.com/" target="_blank" rel="noopener">WhatsApp</a></div></div><button class="btn" id="siteFeedback" data-feature="feedback">✉ Отзыв о сайте</button></aside>
<main class="main">
<div class="filters panel">
<div class="filter-title">ВЫБРАТЬ ВИДЕОКАРТЫ</div>
<div class="gpu-filter all-filter" data-scope="all"><input id="filter-all" type="text" placeholder="Все видеокарты" autocomplete="off" aria-label="Поиск среди всех видеокарт"><button type="button" class="filter-arrow" aria-label="Показать список">▾</button><div class="filter-options"></div></div>
<div class="gpu-filter nvidia-filter" data-scope="NVIDIA"><input id="filter-nvidia" type="text" placeholder="NVIDIA — выбрать модель" autocomplete="off" aria-label="Поиск видеокарт NVIDIA"><button type="button" class="filter-arrow" aria-label="Показать список NVIDIA">▾</button><div class="filter-options"></div></div>
<div class="gpu-filter amd-filter" data-scope="AMD"><input id="filter-amd" type="text" placeholder="AMD — выбрать модель" autocomplete="off" aria-label="Поиск видеокарт AMD"><button type="button" class="filter-arrow" aria-label="Показать список AMD">▾</button><div class="filter-options"></div></div>
<div class="gpu-filter intel-filter" data-scope="INTEL"><input id="filter-intel" type="text" placeholder="INTEL — выбрать модель" autocomplete="off" aria-label="Поиск видеокарт Intel"><button type="button" class="filter-arrow" aria-label="Показать список Intel">▾</button><div class="filter-options"></div></div>
<div class="gpu-filter retro-filter" data-scope="retro"><input id="filter-retro" type="text" placeholder="Ретро карты до 2015г" autocomplete="off" aria-label="Поиск ретро видеокарт до 2015 года"><button type="button" class="filter-arrow" aria-label="Показать список ретро видеокарт">▾</button><div class="filter-options"></div></div>
<div class="gpu-filter favorites-filter" data-scope="favorites"><input id="filter-favorites" type="text" placeholder="★ Избранные" autocomplete="off" aria-label="Избранные видеокарты"><button type="button" class="filter-arrow" aria-label="Показать избранные">▾</button><div class="filter-options"></div></div>
</div>
<section class="table-panel panel"><div class="table-viewport" id="viewport"><table id="gpuTable"><thead><tr><th class="remove-column-head" aria-label="Удалить из сравнения"></th><th>Видеокарта</th><th>Релиз</th><th>Техпроцесс / ядро</th><th>Частота ядра<small>база-буст</small></th><th title="Шейдерные блоки / блоки растеризации (ROP) / текстурные блоки (TMU)">Шейдерные блоки / ROP / TMU</th><th class="nc">GPixel/s / GTexel/s</th><th>Тип памяти / объём / частота</th><th>Шина / пропускная способность</th><th>TDP / рекомендуемый БП</th><th title="У AMD — AI-ускорители, у Intel — XMX-блоки">Тензорные ядра / RT-ядра</th><th>Вендор / название ядра</th><th>Интерфейс / DirectX</th></tr></thead><tbody>
<?= renderGpuRows($gpus) ?>
</tbody></table><div class="tgrid" id="gridT" role="table" aria-label="Сравнение видеокарт: видеокарты в столбцах"></div></div><div class="table-bottom"><span class="bottom-spacer"></span><div class="bottom-actions"><button class="btn" id="orientToggle" type="button" aria-pressed="false" title="Повернуть таблицу: видеокарты в столбцах"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16M3 9h18M13 6l2 2-2 2M17 14l-2 2 2 2"/></svg></button><button class="btn" id="clearSearch" type="button" aria-label="Очистить запрос" title="Очистить запрос"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M2.4 9.1 1.9 11.1 3.6 11.1 5.0 10.9 5.4 11.3 5.2 11.7 4.7 11.9 1.9 12.9 2.3 14.6 2.7 15.8 4.9 15.1 6.7 14.3 7.0 14.4 7.2 14.6 7.1 15.0 6.4 15.8 3.9 17.9 4.8 19.0 6.7 20.7 8.9 22.0 9.9 21.4 10.2 21.5 10.4 21.8 9.9 22.4 11.2 22.8 12.8 21.7 14.4 20.2 14.7 20.2 15.0 20.4 15.0 20.7 13.4 23.0 15.0 22.9 16.1 21.0 17.0 18.9 17.6 16.6 17.8 14.7 9.6 8.1 7.8 8.8 5.9 9.1 3.9 9.2ZM10.6 7.5 18.2 13.7 18.1 13.1 17.8 12.7 12.0 7.9 11.3 7.5ZM22.0 1.0 21.0 1.3 20.4 1.9 18.1 5.8 16.8 7.2 16.4 7.2 15.2 6.6 14.0 6.3 13.1 6.5 12.5 6.9 18.2 11.6 18.5 10.8 18.4 9.9 18.0 8.9 17.1 7.8 17.1 7.4 18.2 5.9 21.5 2.8 22.0 1.9Z"/></svg></button><button class="btn primary icon-only" id="shareComparison" type="button" aria-label="Поделиться сравнением" title="Поделиться сравнением"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><path d="m8.7 10.7 6.6-4.4M8.7 13.3l6.6 4.4"></path></svg></button></div></div></section><section class="perf-panel" id="perfPanel"><div class="perf-list" id="perfList"></div><div class="perf-empty" id="perfEmpty" aria-hidden="true"></div></section></main>
<aside class="rightbar"><section class="right-card panel drawing-card"><div class="drawing-head">Заметки</div><div class="drawing-tools" role="toolbar" aria-label="Инструменты заметки"><button class="btn drawing-tool active" id="drawTool" type="button" aria-pressed="true" title="Рисовать">✎</button><button class="btn drawing-tool" id="textTool" type="button" aria-pressed="false" title="Добавить текст">T</button><button class="btn drawing-tool" id="undoDrawing" type="button" title="Отменить последнее действие (Ctrl+Z)" aria-label="Отменить последнее действие">↶</button><button class="btn drawing-tool" id="clearDrawing" type="button" title="Очистить лист" aria-label="Очистить лист"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M2.4 9.1 1.9 11.1 3.6 11.1 5.0 10.9 5.4 11.3 5.2 11.7 4.7 11.9 1.9 12.9 2.3 14.6 2.7 15.8 4.9 15.1 6.7 14.3 7.0 14.4 7.2 14.6 7.1 15.0 6.4 15.8 3.9 17.9 4.8 19.0 6.7 20.7 8.9 22.0 9.9 21.4 10.2 21.5 10.4 21.8 9.9 22.4 11.2 22.8 12.8 21.7 14.4 20.2 14.7 20.2 15.0 20.4 15.0 20.7 13.4 23.0 15.0 22.9 16.1 21.0 17.0 18.9 17.6 16.6 17.8 14.7 9.6 8.1 7.8 8.8 5.9 9.1 3.9 9.2ZM10.6 7.5 18.2 13.7 18.1 13.1 17.8 12.7 12.0 7.9 11.3 7.5ZM22.0 1.0 21.0 1.3 20.4 1.9 18.1 5.8 16.8 7.2 16.4 7.2 15.2 6.6 14.0 6.3 13.1 6.5 12.5 6.9 18.2 11.6 18.5 10.8 18.4 9.9 18.0 8.9 17.1 7.8 17.1 7.4 18.2 5.9 21.5 2.8 22.0 1.9Z"/></svg></button></div><div class="drawing-paper-wrap"><canvas id="drawingCanvas" aria-label="Поле для рисования и заметок"></canvas></div><div class="drawing-footer"><button class="btn" id="saveDrawing">Сохранить заметку</button><button class="btn send-drawing" id="sendDrawing">Поделиться</button></div></section></aside>
</div></div>
<div class="cookie-bar" id="cookieBar" data-feature="cookies" role="region" aria-label="Уведомление об использовании cookie"><p>Сайт использует cookie и память браузера, чтобы запоминать ваши настройки: тему, избранное и заметки. Продолжая пользоваться сайтом, вы соглашаетесь с этим.</p><div class="cookie-actions"><button class="btn" id="cookieMore" type="button">Подробнее</button><button class="btn primary" id="cookieOk" type="button">Понятно</button></div></div>
<div class="modal-backdrop" id="modalBackdrop" role="dialog" aria-modal="true" aria-labelledby="modalTitle"><div class="site-modal"><button class="modal-close" id="modalClose" aria-label="Закрыть">×</button><h2 id="modalTitle">Связаться с GPU Compare</h2><div id="modalBody"></div><div class="modal-actions"><button class="btn" id="modalCancel">Закрыть</button><button class="btn primary" id="modalSubmit">Продолжить</button></div></div></div>
<script>
const viewport=document.getElementById('viewport'), table=document.getElementById('gpuTable');
let activeFilter='all', hoverMode=true, raf=0;
// Position-based scroll: cursor at the viewport's center = table's center;
// cursor left/right of center moves the horizontal view proportionally.
viewport.addEventListener('pointermove', (event) => {
    if (window.rowDragging || event.pointerType !== 'mouse') return;
    const rect = viewport.getBoundingClientRect();

    // Интерактивная зона: от 25% до 92% ширины. Правее 92% — сразу конец таблицы.
    // (Раньше конец был только в последних 1% ширины — это ~10 px у самого края, где ещё и полоса прокрутки:
    //  при быстром движении мыши или другой ширине полосы до конца таблицы можно было не доехать.)
    const zoneStart = rect.width * 0.25;
    const zoneEnd = rect.width * 0.92;

    // Положение мыши относительно левого края таблицы
    const mouseX = event.clientX - rect.left;

    // Левее зоны прокрутка не управляется (как и раньше — там закреплённые столбцы)
    if (mouseX < zoneStart) {
        return;
    }

    // Преобразуем положение мыши в диапазон от 0 до 1 (правее зоны — 1, то есть конец таблицы)
    const progress = Math.min(1, (mouseX - zoneStart) / (zoneEnd - zoneStart));

    // Прокручиваем таблицу слева направо
    const maxScroll = viewport.scrollWidth - viewport.clientWidth;
    viewport.scrollLeft = progress * maxScroll;
});
viewport.addEventListener('pointerleave',()=>{if(raf)cancelAnimationFrame(raf)});


const filterBoxes = [...document.querySelectorAll('.gpu-filter')];
const catalogRows = [...table.querySelectorAll('tbody tr')];
let selectedModel = '';
// Каталог хранится в строках таблицы, но до выбора модели они не участвуют в сравнении.
catalogRows.forEach(tr => { tr.dataset.selected = 'false'; tr.setAttribute('data-catalog', '1'); });
function rowsForScope(scope) {
  return catalogRows.filter(tr => {
    if (scope === 'all') return true;
    if (scope === 'favorites') return tr.dataset.favorite === 'true';
    if (scope === 'retro') return tr.dataset.year !== '' && Number(tr.dataset.year) < 2015;
    return tr.dataset.brand === scope;
  });
}
function closeFilterMenus(except) {
  filterBoxes.forEach(box => { if (box !== except) box.classList.remove('open'); });
}
function updateCounts() {
  const selected = catalogRows.filter(tr => tr.dataset.selected === 'true');
  const shown = document.getElementById('shown');
  if (shown) shown.textContent = 'В сравнении ' + selected.length + ' видеокарт · в базе ' + catalogRows.length;
  if (perfReady) renderPerf();
  if (tReady) renderT();
}
const favoriteStorageKey = 'gpuCompareFavoritesV1';
let favoriteNames = new Set();
try { favoriteNames = new Set(JSON.parse(localStorage.getItem(favoriteStorageKey) || '[]')); } catch(e) {}
function persistFavorites() { try { localStorage.setItem(favoriteStorageKey, JSON.stringify([...favoriteNames])); } catch(e) {} }
function gpuName(tr) { return tr.querySelector('.brand-name').textContent.trim(); }
function renderRemoveCell(tr) {
  const cell = tr.querySelector('.remove-cell');
  cell.replaceChildren();
  if (tr.dataset.selected === 'true') {
    const remove = document.createElement('button');
    remove.type = 'button'; remove.className = 'remove-gpu'; remove.innerHTML = '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 10v6M14 10v6"/></svg>';
    remove.title = 'Убрать видеокарту из сравнения';
    remove.setAttribute('aria-label', 'Убрать ' + gpuName(tr) + ' из сравнения');
    remove.addEventListener('click', () => { tr.dataset.selected = 'false'; tr.setAttribute('data-catalog', '1'); renderRemoveCell(tr); updateCounts(); });
    cell.appendChild(remove);
  }
  const star = document.createElement('button');
  star.type = 'button'; star.className = 'favorite-star' + (tr.dataset.favorite === 'true' ? ' is-favorite' : '');
  star.textContent = tr.dataset.favorite === 'true' ? '★' : '☆';
  star.title = tr.dataset.favorite === 'true' ? 'Убрать из избранного' : 'Добавить в избранное';
  star.setAttribute('aria-label', star.title + ': ' + gpuName(tr));
  star.addEventListener('click', () => {
    const name = gpuName(tr);
    if (favoriteNames.has(name)) favoriteNames.delete(name); else favoriteNames.add(name);
    tr.dataset.favorite = favoriteNames.has(name) ? 'true' : 'false';
    persistFavorites(); renderRemoveCell(tr);
    filterBoxes.forEach(box => { if (box.dataset.scope === 'favorites' && box.classList.contains('open')) updateFilterOptions(box); });
  });
  cell.appendChild(star);
}

function addGpu(tr) {
  if (tr.dataset.selected === 'true') {
    tr.removeAttribute('data-catalog');
    tr.classList.add('already-selected');
    setTimeout(() => tr.classList.remove('already-selected'), 650);
    return;
  }
  tr.dataset.selected = 'true';
  table.tBodies[0].appendChild(tr);
  tr.removeAttribute('data-catalog');
  renderRemoveCell(tr);
  updateCounts();
}

// Счётчик популярности: шлём id карты в track.php, обновляем data-popularity локально.
function bumpPopularity(tr) {
  const id = tr.dataset.id;
  if (!id) return;
  try {
    fetch('track.php', {method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'id=' + encodeURIComponent(id)}).catch(() => {});
    tr.dataset.popularity = String(Number(tr.dataset.popularity || 0) + 1);
  } catch (e) {}
}

function updateFilterOptions(box) {
  const input = box.querySelector('input');
  const options = box.querySelector('.filter-options');
  const q = input.value.trim().toLowerCase();
  const matches = rowsForScope(box.dataset.scope).filter(tr => {
    const name = tr.querySelector('.brand-name').textContent.toLowerCase();
    return name.includes(q) || tr.dataset.year.includes(q) || tr.dataset.brand.toLowerCase().includes(q);
  });
  // Сортировка: сначала самые популярные (больше раз выбирали), затем более короткие названия.
  matches.sort((a, b) => (Number(b.dataset.popularity || 0) - Number(a.dataset.popularity || 0))
                      || (a.querySelector('.brand-name').textContent.length - b.querySelector('.brand-name').textContent.length));
  options.innerHTML = '';
  if (!matches.length) {
    const empty = document.createElement('div');
    empty.className = 'filter-option';
    empty.textContent = box.dataset.scope === 'favorites' ? 'Пока нет избранных видеокарт' : 'Ничего не найдено';
    empty.style.cursor = 'default';
    options.appendChild(empty);
    return;
  }
  matches.forEach(tr => {
    const item = document.createElement('button');
    item.type = 'button';
    item.className = 'filter-option';
    item.textContent = (tr.dataset.selected === 'true' ? '✓ ' : '+ ') + (tr.dataset.favorite === 'true' ? '★ ' : '') + tr.querySelector('.brand-name').textContent;
    item.addEventListener('click', () => {
      addGpu(tr);
      bumpPopularity(tr); // увеличиваем счётчик выборов на сервере
      // Текст запроса (например, «5090») остаётся в поле — можно выбрать следующую карту из того же списка.
      closeFilterMenus(null);
      updateCounts();
    });
    options.appendChild(item);
  });
}
filterBoxes.forEach(box => {
  const input = box.querySelector('input');
  const arrow = box.querySelector('.filter-arrow');
  input.addEventListener('focus', () => { closeFilterMenus(box); updateFilterOptions(box); box.classList.add('open'); });
  input.addEventListener('input', () => {
    selectedModel = '';
    closeFilterMenus(box);
    updateFilterOptions(box);
    box.classList.add('open');
  });
  arrow.addEventListener('click', () => {
    const wasOpen = box.classList.contains('open');
    closeFilterMenus(box);
    if (!wasOpen) { updateFilterOptions(box); box.classList.add('open'); if (!matchMedia('(hover:none)').matches) input.focus(); }
  });
  input.addEventListener('keydown', event => {
    if (event.key === 'Escape') box.classList.remove('open');
    if (event.key === 'Enter') {
      const first = box.querySelector('.filter-option');
      if (first && first.tagName === 'BUTTON') first.click();
    }
  });
});
document.addEventListener('click', event => {
  if (!event.target.closest('.gpu-filter')) closeFilterMenus(null);
});
document.getElementById('clearSearch').addEventListener('click', () => {
  filterBoxes.forEach(box => { box.querySelector('input').value = ''; box.classList.remove('open'); });
  catalogRows.forEach(tr => { tr.dataset.selected = 'false'; tr.setAttribute('data-catalog', '1'); renderRemoveCell(tr); });
  updateCounts();
});

function applyFilters() { updateCounts(); }
updateCounts();
// Рисовалка: карандаш с курсором, текст прямо на листе и надёжная отмена.
const drawingCanvas = document.getElementById('drawingCanvas');
const drawingCtx = drawingCanvas.getContext('2d');
const drawingCard = document.querySelector('.drawing-card');
const drawingPaper = document.querySelector('.drawing-paper-wrap');
let drawing = false, erasing = false, lastPoint = null, drawingMode = 'draw';
let drawingHistory = [];
const drawingStorageKey = 'gpuCompareDrawingNoteV1';
function saveDrawingSnapshot(){try{drawingHistory.push(drawingCtx.getImageData(0,0,drawingCanvas.width,drawingCanvas.height));if(drawingHistory.length>50)drawingHistory.shift();}catch(e){}}
function restoreDrawingSnapshot(data){if(!data)return;drawingCtx.save();drawingCtx.setTransform(1,0,0,1,0,0);drawingCtx.clearRect(0,0,drawingCanvas.width,drawingCanvas.height);drawingCtx.putImageData(data,0,0);drawingCtx.restore();}
function undoDrawing(){if(!drawingHistory.length)return;restoreDrawingSnapshot(drawingHistory.pop());}
function resizeDrawingCanvas(){
  const rect=drawingCanvas.getBoundingClientRect();if(!rect.width||!rect.height)return;
  const oldCanvas=document.createElement('canvas');oldCanvas.width=drawingCanvas.width;oldCanvas.height=drawingCanvas.height;
  if(oldCanvas.width&&oldCanvas.height)oldCanvas.getContext('2d').drawImage(drawingCanvas,0,0);
  const dpr=window.devicePixelRatio||1;drawingCanvas.width=Math.round(rect.width*dpr);drawingCanvas.height=Math.round(rect.height*dpr);
  drawingCtx.setTransform(dpr,0,0,dpr,0,0);drawingCtx.lineCap='round';drawingCtx.lineJoin='round';
  if(oldCanvas.width&&oldCanvas.height)drawingCtx.drawImage(oldCanvas,0,0,oldCanvas.width,oldCanvas.height,0,0,rect.width,rect.height);
  drawingHistory=[];
}
function getDrawingPoint(event){const rect=drawingCanvas.getBoundingClientRect();return{x:event.clientX-rect.left,y:event.clientY-rect.top};}
function getDrawingTextColor(){return document.body.classList.contains('theme-light')?'#111111':'#ffffff';}
function setDrawingMode(mode){const activeEditor=drawingPaper.querySelector('.drawing-text-editor');if(activeEditor)finishInlineText(activeEditor,true);drawingMode=mode;drawingCard.classList.toggle('text-mode',mode==='text');document.getElementById('drawTool').classList.toggle('active',mode==='draw');document.getElementById('textTool').classList.toggle('active',mode==='text');document.getElementById('drawTool').setAttribute('aria-pressed',String(mode==='draw'));document.getElementById('textTool').setAttribute('aria-pressed',String(mode==='text'));}
function finishInlineText(editor,commit=true){
  if(!editor||!editor.isConnected)return;
  const text=editor.innerText.replace(/\u00a0/g,' ').trim();
  try{if(commit&&text){saveDrawingSnapshot();const rect=drawingCanvas.getBoundingClientRect();const x=parseFloat(editor.dataset.x)||0,y=parseFloat(editor.dataset.y)||0;drawingCtx.save();drawingCtx.fillStyle=getDrawingTextColor();drawingCtx.font='16px Segoe UI, Arial, sans-serif';const lines=text.split('\n');lines.forEach((line,i)=>drawingCtx.fillText(line,x,y+16+i*21,Math.max(30,rect.width-x-8)));drawingCtx.restore();}}catch(err){console.error('Не удалось сохранить текст на листе',err);}finally{editor.remove();}
}
drawingPaper.addEventListener('pointerdown',e=>{const editor=drawingPaper.querySelector('.drawing-text-editor');if(editor&&!editor.contains(e.target)&&e.target!==drawingCanvas)finishInlineText(editor,true);});
drawingCanvas.addEventListener('contextmenu',e=>e.preventDefault());
drawingCanvas.addEventListener('pointerdown',event=>{
  if(drawingMode==='text'){
    event.preventDefault();const point=getDrawingPoint(event);
    const existing=drawingPaper.querySelector('.drawing-text-editor');if(existing)finishInlineText(existing,true);
    const editor=document.createElement('div');editor.className='drawing-text-editor';editor.contentEditable='true';editor.setAttribute('role','textbox');editor.setAttribute('aria-label','Текст заметки');editor.dataset.x=String(Math.max(4,Math.min(point.x,drawingCanvas.clientWidth-100)));editor.dataset.y=String(Math.max(0,Math.min(point.y,drawingCanvas.clientHeight-24)));editor.style.left=editor.dataset.x+'px';editor.style.top=editor.dataset.y+'px';editor.style.setProperty('--draw-editor-bg',document.body.classList.contains('theme-light')?'#ffffff':'#101820');editor.style.setProperty('--draw-editor-color',document.body.classList.contains('theme-light')?'#111111':'#ffffff');drawingPaper.appendChild(editor);editor.focus();
    editor.addEventListener('keydown',e=>{if(e.key==='Escape'){e.preventDefault();finishInlineText(editor,false);drawingCanvas.focus();}else if((e.ctrlKey||e.metaKey)&&e.key==='Enter'){e.preventDefault();finishInlineText(editor,true);drawingCanvas.focus();}else if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='z'){e.preventDefault();undoDrawing();}});
    editor.addEventListener('blur',()=>finishInlineText(editor,true),{once:true});return;
  }
  if(event.button!==0&&event.button!==2)return;event.preventDefault();saveDrawingSnapshot();drawing=true;erasing=event.button===2;lastPoint=getDrawingPoint(event);drawingCanvas.setPointerCapture(event.pointerId);
  drawingCtx.globalCompositeOperation=erasing?'destination-out':'source-over';drawingCtx.beginPath();drawingCtx.arc(lastPoint.x,lastPoint.y,erasing?12:1.4,0,Math.PI*2);drawingCtx.fillStyle='#c4dff7';drawingCtx.fill();drawingCtx.beginPath();drawingCtx.moveTo(lastPoint.x,lastPoint.y);
});
drawingCanvas.addEventListener('pointermove',event=>{if(!drawing||!lastPoint)return;const point=getDrawingPoint(event);drawingCtx.globalCompositeOperation=erasing?'destination-out':'source-over';drawingCtx.strokeStyle='#c4dff7';drawingCtx.lineWidth=erasing?24:2.4;drawingCtx.lineTo(point.x,point.y);drawingCtx.stroke();drawingCtx.beginPath();drawingCtx.moveTo(point.x,point.y);lastPoint=point;});
function stopDrawing(){drawing=false;lastPoint=null;drawingCtx.beginPath();drawingCtx.globalCompositeOperation='source-over';}
drawingCanvas.addEventListener('pointerup',stopDrawing);drawingCanvas.addEventListener('pointercancel',stopDrawing);drawingCanvas.addEventListener('lostpointercapture',stopDrawing);
document.getElementById('drawTool').addEventListener('click',()=>setDrawingMode('draw'));document.getElementById('textTool').addEventListener('click',()=>setDrawingMode('text'));document.getElementById('undoDrawing').addEventListener('click',undoDrawing);
window.addEventListener('keydown',e=>{if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='z'&&!e.target.closest('input,textarea,[contenteditable="true"]')){e.preventDefault();e.stopPropagation();undoDrawing();}},true);
window.addEventListener('resize',resizeDrawingCanvas);resizeDrawingCanvas();
try{const saved=localStorage.getItem(drawingStorageKey);if(saved){const img=new Image();img.onload=()=>drawingCtx.drawImage(img,0,0,drawingCanvas.clientWidth,drawingCanvas.clientHeight);img.src=saved;}}catch(e){}
// Restore favorite flags and make the remove/star controls available on selected rows.
catalogRows.forEach(tr => {
  const name = gpuName(tr);
  tr.dataset.favorite = favoriteNames.has(name) ? 'true' : 'false';
  renderRemoveCell(tr);
});
// Modal helpers: these are front-end placeholders until an account/contact backend is connected.
const modalBackdrop = document.getElementById('modalBackdrop'), modalTitle = document.getElementById('modalTitle'), modalBody = document.getElementById('modalBody'), modalSubmit = document.getElementById('modalSubmit');
let modalAction = null;
function openModal(title, body, actionLabel='Продолжить', action=null) {
  modalTitle.textContent = title; modalBody.innerHTML = body; modalSubmit.textContent = actionLabel; modalAction = action;
  document.querySelector('.site-modal').classList.remove('rate-modal');
  document.getElementById('modalCancel').textContent = 'Закрыть';
  modalBackdrop.classList.add('open');
}
function closeModal(){modalBackdrop.classList.remove('open');modalAction=null;document.querySelector('.site-modal').classList.remove('rate-modal');document.getElementById('modalCancel').textContent='Закрыть';}
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancel').addEventListener('click', closeModal);
let modalPointerStartedOutside=false;
modalBackdrop.addEventListener('pointerdown', e => { modalPointerStartedOutside = e.target===modalBackdrop; });
modalBackdrop.addEventListener('click', e => { if(e.target===modalBackdrop && modalPointerStartedOutside) closeModal(); modalPointerStartedOutside=false; });
modalSubmit.addEventListener('click', () => { if(modalAction) modalAction(); else closeModal(); });
document.getElementById('loginButton').addEventListener('click', () => openModal('Вход и регистрация', '<p>В демонстрационной версии аккаунты ещё не подключены. После подключения регистрации избранные видеокарты будут синхронизироваться между устройствами.</p><input type="email" placeholder="Электронная почта" aria-label="Электронная почта"><input type="password" placeholder="Пароль" aria-label="Пароль">', 'Понятно'));
const pageInfo = {
  processors:['Сравнение процессоров','<p>Находится в разработке</p>'],
  error:['Нашли ошибку?','<p>Сообщите модель видеокарты, характеристику с ошибкой и правильное значение (если оно известно).</p><textarea id="modalMessage" rows="4" placeholder="Опишите ошибку"></textarea>'],
  contact:['Напишите нам','<input id="contactEmail" type="email" placeholder="Ваш email (необязательно)"><textarea id="modalMessage" rows="4" placeholder="Ваше сообщение"></textarea>'],
  faq:['Часто задаваемые вопросы о видеокартах','<article class="faq-page"><p class="faq-intro">Ответы на популярные вопросы о сравнении видеокарт NVIDIA GeForce, AMD Radeon и Intel Arc, игровых FPS, видеопамяти и выборе GPU для компьютера.</p><h2>Как сравнить видеокарты между собой?</h2><p>Выберите две или несколько моделей в разделе сравнения видеокарт. Сопоставляйте поколение графического процессора, частоты, количество вычислительных блоков, тип и объём видеопамяти, пропускную способность, энергопотребление и требования к блоку питания. Одной характеристики недостаточно, чтобы определить победителя во всех задачах.</p><h2>Какая видеокарта лучше для игр?</h2><p>Лучшая видеокарта зависит от бюджета, разрешения монитора, игр и желаемой частоты кадров. Для корректного выбора сравнивайте независимые игровые тесты в одинаковом разрешении и на одинаковых настройках. Самая дорогая модель нужна не каждому.</p><h2>Как узнать, сколько FPS будет в игре?</h2><p>FPS зависит не только от видеокарты, но и от процессора, оперативной памяти, разрешения, графических настроек, драйверов и конкретной игры. Ищите тесты именно в тех играх и условиях, в которых планируете играть. Результат одной игры нельзя автоматически переносить на все остальные.</p><h2>Что важнее: объём видеопамяти или мощность видеокарты?</h2><p>Важны оба параметра. Объём VRAM влияет на возможность использовать текстуры высокого качества и работать с большими сценами, но сам по себе не гарантирует высокий FPS. Сравнивайте видеопамять вместе с реальными тестами производительности и пропускной способностью.</p><h2>Что означают NVIDIA Blackwell, AMD RDNA и Intel Xe?</h2><p>Это названия архитектур графических процессоров. Архитектура определяет устройство GPU и поддерживаемые возможности. При сравнении старых и новых моделей учитывайте не только поколение, но и результаты тестов, объём памяти и поддержку нужных технологий.</p><h2>Что лучше — NVIDIA GeForce или AMD Radeon?</h2><p>Универсального ответа нет. Сравните производительность в ваших играх, трассировку лучей, технологии масштабирования изображения, кодирование видео, поддержку программ и энергопотребление. Для рабочих задач совместимость с конкретным приложением может быть важнее игрового рейтинга.</p><h2>Подойдёт ли видеокарта к моему компьютеру?</h2><p>Перед покупкой проверьте длину и толщину карты, свободное место в корпусе, мощность и разъёмы блока питания, слот PCI Express и возможности охлаждения. Также убедитесь, что процессор не будет существенно ограничивать производительность в ваших играх.</p><h2>Какой блок питания нужен для видеокарты?</h2><p>Ориентируйтесь на рекомендации производителя конкретной модели, разъёмы питания и энергопотребление всей системы. Учитывайте качество блока питания и запас мощности; не выбирайте его только по максимальной цифре на наклейке.</p><h2>Как сравнивать видеокарты для 1080p, 1440p и 4K?</h2><p>Чем выше разрешение, тем больше нагрузка на GPU и видеопамять. Сравнивайте FPS отдельно для Full HD (1080p), 1440p и 4K: производительность и относительный разрыв между моделями могут отличаться в зависимости от разрешения и игры.</p><h2>Можно ли сравнивать мобильную видеокарту ноутбука с настольной?</h2><p>Нужно учитывать конкретную модель, лимит мощности, охлаждение и настройки ноутбука. Названия с одинаковым номером не всегда означают одинаковую производительность в мобильной и настольной версиях. Лучше опираться на тесты конкретного устройства.</p><h2>Что такое частота ядра, шина памяти и TDP?</h2><p>Частота ядра показывает рабочую частоту GPU; шина памяти влияет на обмен данными с видеопамятью; TDP/TGP помогает оценить тепловые и энергетические требования, но определения различаются у производителей. Ни один из этих показателей отдельно не является точным рейтингом производительности.</p><h2>Можно ли доверять одному рейтингу видеокарт?</h2><p>Любой общий рейтинг упрощает картину: порядок моделей меняется в зависимости от игры, разрешения и технологий. Для выбора используйте несколько независимых тестов и проверяйте условия измерений. Если данные на сайте обозначены как ориентировочные, не воспринимайте их как гарантированный FPS.</p><h2>Как сообщить об ошибке в характеристиках?</h2><p>Откройте пункт «Нашли ошибку?» и укажите модель видеокарты, характеристику и ссылку на источник, если он есть. Это поможет проверить информацию и исправить возможную неточность.</p></article>']
};
// Форма пользовательской оценки: все три значения можно вводить вручную.
const popularGames = [
 'Counter-Strike 2','Dota 2','Cyberpunk 2077','Grand Theft Auto V','Grand Theft Auto VI',
 'Fortnite','PUBG: Battlegrounds','Call of Duty: Warzone','Apex Legends','Minecraft',
 'Hogwarts Legacy','Red Dead Redemption 2','The Witcher 3: Wild Hunt','Baldur’s Gate 3',
 'Helldivers 2','Forza Horizon 5','Alan Wake 2','Escape from Tarkov','World of Tanks',
 'Valorant','Overwatch 2','Starfield','S.T.A.L.K.E.R. 2: Heart of Chornobyl','Black Myth: Wukong',
 'League of Legends','A Plague Tale: Requiem','Assassin’s Creed Shadows','Assassin’s Creed Valhalla',
 'Battlefield 2042','Battlefield 6','Call of Duty: Black Ops 6','Diablo IV','Elden Ring',
 'F1 25','Farming Simulator 25','Final Fantasy XVI','God of War Ragnarök','Horizon Forbidden West',
 'Kingdom Come: Deliverance II','Marvel Rivals','Microsoft Flight Simulator 2024','Monster Hunter Wilds',
 'Path of Exile 2','Resident Evil 4','Silent Hill 2','The Last of Us Part I','The Last of Us Part II',
 'The Sims 4','War Thunder','Warframe','World of Warcraft'
];
const gameAliases = {
 'Counter-Strike 2':['кс2','кс 2','кс','контр страйк 2','контрстрайк 2','контр страйк','контрстрайк','контра','counter strike','cs2','ксго','cs go','ксерка'],
 'Dota 2':['дота 2','дота','дота2'],
 'Cyberpunk 2077':['киберпанк','кибер панк','киберпанк 2077','cyberpunk'],
 'Grand Theft Auto V':['гта 5','гта5','гта в','гта 5','gta 5','gta v','гта','гта 5 рп','гта онлайн'],
 'Grand Theft Auto VI':['гта 6','гта6','gta 6','gta vi'],
 'Fortnite':['фортнайт','фортнайт','форт'],
 'PUBG: Battlegrounds':['пубг','пабг','пабджи','пабг батлграундс','pubg'],
 'Call of Duty: Warzone':['кол оф дьюти','колда','варзон','warzone','кал оф дьюти','кол оф дьюти варзон'],
 'Apex Legends':['апекс','апекс легендс','апекс легенды'],
 'Minecraft':['майнкрафт','минекрафт','майн','minecraft'],
 'Hogwarts Legacy':['хогвартс','хогвартс легаси','хогвартс наследие'],
 'Red Dead Redemption 2':['ред дед редемпшен 2','ред дед 2','рдр 2','рдр2','rdr2','ред дед'],
 'The Witcher 3: Wild Hunt':['ведьмак','ведьмак 3','ведьмак 3 дикая охота','ведьмак дикая охота'],
 'Baldur’s Gate 3':['балдурс гейт 3','балдурс гейт','балдур','бг3'],
 'Helldivers 2':['хеллдайверс 2','хеллдайверс','хелл дайверс'],
 'Forza Horizon 5':['форза хорайзон 5','форза горизонт 5','форза 5','форза'],
 'Alan Wake 2':['алан вейк 2','алан вейк'],
 'Escape from Tarkov':['тарков','эскейп фром тарков','побег из таркова'],
 'World of Tanks':['мир танков','ворлд оф танкс','вот','wot','танки'],
 'Valorant':['валорант','валик'],
 'Overwatch 2':['оверwatch 2','оверватч 2','оверватч','овервотч'],
 'Starfield':['старфилд'],
 'S.T.A.L.K.E.R. 2: Heart of Chornobyl':['сталкер 2','сталкер','сталкер сердце чернобыля','stalker 2'],
 'Black Myth: Wukong':['блэк миф вуконг','блэк миф','black myth','вуконг'],
 'League of Legends':['лига легенд','лол','league of legends'],
 'A Plague Tale: Requiem':['плаг тейл','плейг тейл','чума реквием'],
 'Assassin’s Creed Shadows':['ассасин крид шэдоус','ассасин шэдоус','ассасинс крид'],
 'Assassin’s Creed Valhalla':['ассасин крид вальгалла','вальгалла'],
 'Battlefield 2042':['батлфилд 2042','батла 2042','батлфилд'],
 'Battlefield 6':['батлфилд 6','батла 6'],
 'Call of Duty: Black Ops 6':['блэк опс 6','блэк опс','black ops 6','колда блэк опс'],
 'Diablo IV':['диабло 4','диабло iv','диабло'],
 'Elden Ring':['элден ринг','елден ринг','elden ring'],
 'F1 25':['ф1 25','формула 1 25','f1 25'],
 'Farming Simulator 25':['фарминг симулятор 25','симулятор фермера','фермер симулятор'],
 'Final Fantasy XVI':['финал фэнтези 16','финал фэнтези xvi','final fantasy 16'],
 'God of War Ragnarök':['год оф вар рагнарёк','год оф вар рагнарек','бог войны рагнарёк','год оф вар'],
 'Horizon Forbidden West':['хорайзон форбидден вест','хорайзон запретный запад','horizon forbidden west'],
 'Kingdom Come: Deliverance II':['кингдом кам 2','кингдом кам деливеранс 2','kingdom come 2'],
 'Marvel Rivals':['марвел райвалс','марвел ривалс','ривалс'],
 'Microsoft Flight Simulator 2024':['симулятор полётов','симулятор полетов','флайт симулятор','microsoft flight simulator'],
 'Monster Hunter Wilds':['монстер хантер вайлдс','монстер хантер','monster hunter'],
 'Path of Exile 2':['путь изгнанника 2','пое 2','поэ 2','path of exile 2'],
 'Resident Evil 4':['резидент ивил 4','обитель зла 4','резидент эвил 4'],
 'Silent Hill 2':['сайлент хилл 2','сайлент хилл'],
 'The Last of Us Part I':['одни из нас','ласт оф ас','the last of us','последние из нас'],
 'The Last of Us Part II':['одни из нас 2','ласт оф ас 2','последние из нас 2'],
 'The Sims 4':['симс 4','симс','sims 4'],
 'War Thunder':['вар тандер','вар тандер','war thunder'],
 'Warframe':['варфрейм','warframe'],
 'World of Warcraft':['ворлд оф варкрафт','варкрафт','вов','wow']
};
function gameMatches(query){
  const q=query.trim().toLocaleLowerCase('ru'); if(!q)return [];
  return popularGames.filter(g=>g.toLocaleLowerCase('ru').includes(q)||(gameAliases[g]||[]).some(a=>{a=a.toLocaleLowerCase('ru');return a.includes(q)||(a.length>3&&q.includes(a));})).slice(0,8);
}
function showGameSuggestions(input,showAll){
  const wrap=input.closest('.rate-game-wrap'), box=wrap.querySelector('.rate-suggestions'), toggle=wrap.querySelector('.rate-game-toggle');
  const list=showAll?popularGames:gameMatches(input.value);
  if(!list.length||(!showAll&&!input.value.trim())){box.hidden=true;toggle.setAttribute('aria-expanded','false');return;}
  box.innerHTML=list.map(g=>'<button type="button" class="rate-suggestion" data-game="'+escapeRateHtml(g)+'">'+escapeRateHtml(g)+'</button>').join('');
  box.hidden=false;toggle.setAttribute('aria-expanded','true');
}
function hideRateSuggestions(){document.querySelectorAll('.rate-suggestions').forEach(b=>{b.hidden=true;b.parentElement.querySelector('.rate-game-toggle').setAttribute('aria-expanded','false');});}
document.addEventListener('click',e=>{if(!e.target.closest('.rate-game-wrap'))hideRateSuggestions();});
const fpsOptions=Array.from({length:60},(_,i)=>String((i+1)*5)).concat(['350','400','450','500','600']);
const gpuRateOptions=()=>catalogRows.map(tr=>gpuName(tr));
function escapeRateHtml(v){return String(v).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
let rateSeq=0;
function rateSeg(key,label,items,def){
  return '<div class="rate-seg-wrap"><span class="rate-label">'+label+'</span><div class="rate-seg" data-setting="'+key+'">'+items.map(v=>'<button type="button" class="rate-choice'+(v===def?' active':'')+'" data-value="'+v+'">'+v+'</button>').join('')+'</div></div>';
}
function rateRowHtml(){
  const n=++rateSeq, opt=a=>a.map(v=>'<option value="'+escapeRateHtml(v)+'"></option>').join('');
  return '<div class="rate-row"><div class="rate-grid">'
   +'<label class="rate-field"><span class="rate-label">Видеокарта</span><input class="rate-gpu" type="text" list="rg'+n+'" placeholder="Например, GeForce RTX 5090" autocomplete="off"><datalist id="rg'+n+'">'+opt(gpuRateOptions())+'</datalist></label>'
   +'<div class="rate-field"><span class="rate-label">Игра</span><div class="rate-game-wrap"><input class="rate-game" type="text" placeholder="Введите или выберите игру" autocomplete="off" aria-label="Игра"><button class="rate-game-toggle" type="button" aria-label="Список игр" aria-expanded="false">▾</button><div class="rate-suggestions" hidden></div></div></div>'
   +'<label class="rate-field"><span class="rate-label">Средний FPS</span><input class="rate-fps" type="number" inputmode="numeric" min="1" max="1000" list="rf'+n+'" placeholder="FPS"><datalist id="rf'+n+'">'+opt(fpsOptions)+'</datalist></label></div>'
   +'<div class="rate-settings">'+rateSeg('resolution','Разрешение',['Full HD','2K','4K'],'Full HD')+rateSeg('quality','Графика',['Низкие','Средние','Ультра'],'Средние')
   +'<button class="remove-rate-row" type="button">Удалить результат</button></div></div>';
}
function maybeAppendRateRow(){
  const c=document.getElementById('rateRows'); if(!c)return;
  const last=c.lastElementChild;
  if(last&&last.querySelector('.rate-game').value.trim()&&+last.querySelector('.rate-fps').value>0)c.insertAdjacentHTML('beforeend',rateRowHtml());
}
function openRateModal(){
  openModal('Моя видеокарта','<p class="rate-sub">Укажите видеокарту, игру, средний FPS и настройки теста. Игру можно вводить по-русски — подскажем английское название.</p><div id="rateRows">'+rateRowHtml()+'</div><p class="rate-hint">Новая строка добавится сама, когда заполнены игра и FPS.</p>','Отправить результаты',submitRateForm);
  document.querySelector('.site-modal').classList.add('rate-modal');
  document.getElementById('modalCancel').textContent='Отмена';
  const rows=document.getElementById('rateRows');
  rows.addEventListener('input',e=>{if(e.target.matches('.rate-game'))showGameSuggestions(e.target);maybeAppendRateRow();});
  rows.addEventListener('change',maybeAppendRateRow);
  rows.addEventListener('click',e=>{
    const t=e.target, wrap=t.closest('.rate-game-wrap');
    if(t.closest('.rate-game-toggle')){const box=wrap.querySelector('.rate-suggestions');if(box.hidden){showGameSuggestions(wrap.querySelector('.rate-game'),true);box.scrollTop=0;}else hideRateSuggestions();return;}
    const sug=t.closest('.rate-suggestion');
    if(sug){wrap.querySelector('.rate-game').value=sug.dataset.game;hideRateSuggestions();maybeAppendRateRow();return;}
    const ch=t.closest('.rate-choice');
    if(ch){ch.parentElement.querySelectorAll('.rate-choice').forEach(b=>b.classList.toggle('active',b===ch));return;}
    const rm=t.closest('.remove-rate-row');if(rm)rm.closest('.rate-row').remove();
  });
}
function submitRateForm(){
  const rows=[...document.querySelectorAll('#rateRows .rate-row')];
  const results=rows.map(row=>({gpu:row.querySelector('.rate-gpu').value.trim(),game:row.querySelector('.rate-game').value.trim(),fps:row.querySelector('.rate-fps').value,resolution:row.querySelector('[data-setting="resolution"] .rate-choice.active')?.dataset.value||'Full HD',quality:row.querySelector('[data-setting="quality"] .rate-choice.active')?.dataset.value||'Средние'})).filter(x=>x.game||x.fps||x.gpu);
  if(!results.length || results.some(x=>!x.gpu||!x.game||!x.fps||Number(x.fps)<=0)){
    openModal('Заполните результаты','<p>В каждой заполненной строке укажите видеокарту, игру и положительное значение FPS. Названия видеокарты и игры разрешено вводить вручную. Пустую последнюю строку можно оставить без заполнения.</p>','Вернуться к форме',()=>{closeModal();openRateModal();});return;
  }
  const summary=results.map(x=>`<div style="padding:9px 0;border-bottom:1px solid #203b58"><b>${escapeRateHtml(x.gpu)}</b> — ${escapeRateHtml(x.game)}: <b>${escapeRateHtml(x.fps)} FPS</b><br><small style="color:#91aec7">${escapeRateHtml(x.resolution)} · графика: ${escapeRateHtml(x.quality)}</small></div>`).join('');
  openModal('Результаты подготовлены',`<p>Спасибо! Вы указали:</p>${summary}<p>Отправка на сайт пока не подключена. Когда появится серверная часть, эти данные можно будет передавать на проверку и использовать для объективного рейтинга.</p>`,'Понятно');
}

document.getElementById('clearDrawing').addEventListener('click',()=>{const editor=drawingPaper.querySelector('.drawing-text-editor');if(editor)finishInlineText(editor,false);saveDrawingSnapshot();drawingCtx.clearRect(0,0,drawingCanvas.width,drawingCanvas.height);});
document.getElementById('saveDrawing').addEventListener('click',()=>{try{localStorage.setItem(drawingStorageKey,drawingCanvas.toDataURL('image/png'));openModal('Заметка сохранена','<p>Заметка сохранена в этом браузере на этом устройстве. Вы сможете открыть её при следующем посещении сайта.</p>','Понятно');}catch(e){openModal('Не удалось сохранить заметку','<p>В браузере недостаточно места для сохранения. Попробуйте скачать рисунок через «Поделиться».</p>','Закрыть');}});
async function exportDrawingFile(){const blob=await new Promise(resolve=>drawingCanvas.toBlob(resolve,'image/png'));if(!blob){openModal('Не удалось подготовить рисунок','<p>Попробуйте ещё раз.</p>','Закрыть');return null;}return new File([blob],'gpu-compare-zametka.png',{type:'image/png'});}
document.getElementById('sendDrawing').addEventListener('click',()=>{
  openModal('Поделиться заметкой','<p>Выберите, что сделать с рисунком.</p><div class="drawing-share-actions"><button type="button" class="btn primary" id="drawingShareOthers">Поделиться</button><button type="button" class="btn" id="drawingSendUs">Отправить нам</button></div>','Закрыть');
  document.getElementById('drawingSendUs').addEventListener('click',async()=>{const file=await exportDrawingFile();if(!file)return;const blob=new Blob([await file.arrayBuffer()],{type:'image/png'});const url=URL.createObjectURL(blob);const a=document.createElement('a');a.href=url;a.download=file.name;a.click();setTimeout(()=>URL.revokeObjectURL(url),1000);closeModal();openModal('Рисунок подготовлен к отправке','<p>Файл заметки скачан. Прикрепите его к письму или сообщению нам. Автоматическая отправка будет доступна после подключения адреса обратной связи.</p>','Понятно');});
  document.getElementById('drawingShareOthers').addEventListener('click',async()=>{const file=await exportDrawingFile();if(!file)return;try{if(navigator.canShare&&navigator.canShare({files:[file]})){await navigator.share({files:[file],title:'Заметка GPU Compare',text:'Моя заметка'});closeModal();return;}}catch(e){if(e.name==='AbortError')return;}const url=URL.createObjectURL(new Blob([await file.arrayBuffer()],{type:'image/png'}));const a=document.createElement('a');a.href=url;a.download=file.name;a.click();setTimeout(()=>URL.revokeObjectURL(url),1000);closeModal();openModal('Заметка сохранена','<p>Браузер не поддерживает прямую отправку файла — скачайте изображение и прикрепите его к нужному приложению.</p>','Понятно');});
});
document.querySelectorAll('.sidebar .navitem').forEach(item => item.addEventListener('click', () => {
  document.querySelectorAll('.sidebar .navitem').forEach(n => n.classList.remove('active')); item.classList.add('active');
  const page = item.dataset.page; if(page==='compare'){closeModal();return;} if(page==='rate'){openRateModal();return;} if(pageInfo[page]) { openModal(pageInfo[page][0], pageInfo[page][1], 'Понятно'); if(page==='faq') document.querySelector('.site-modal')?.classList.add('faq-reading'); }
}));
document.getElementById('shareComparison').addEventListener('click', async () => {
  const selected = [...table.tBodies[0].rows].filter(tr => tr.dataset.selected==='true').map(tr => gpuName(tr));
  const url = new URL(window.location.href); url.searchParams.set('gpus', selected.join('|'));
  const shareUrl = url.href;
  try { await navigator.clipboard.writeText(shareUrl); openModal('Ссылка скопирована','<p>Ссылка содержит выбранные видеокарты. После размещения сайта на домене ею можно будет поделиться с другими пользователями.</p><input readonly value="'+shareUrl.replace(/&/g,'&amp;').replace(/"/g,'&quot;')+'">','Готово'); }
  catch(e) { openModal('Ссылка на сравнение','<p>Скопируйте ссылку ниже.</p><input id="shareUrlInput" readonly value="'+shareUrl.replace(/&/g,'&amp;').replace(/"/g,'&quot;')+'">','Выделить ссылку',()=>{const f=document.getElementById('shareUrlInput');f.select();document.execCommand('copy');closeModal();}); }
});
document.getElementById('siteFeedback').addEventListener('click', () => openModal('Отзыв о сайте','<p>Что понравилось или что стоит улучшить?</p><textarea id="feedbackText" rows="4" placeholder="Напишите отзыв"></textarea><input id="feedbackEmail" type="email" placeholder="Email для ответа (необязательно)">','Подготовить письмо',()=>{
  const msg=document.getElementById('feedbackText')?.value||''; const email=document.getElementById('feedbackEmail')?.value||'';
  window.location.href='mailto:?subject='+encodeURIComponent('Отзыв о GPU Compare')+'&body='+encodeURIComponent(msg+(email?'\n\nEmail для ответа: '+email:'')); closeModal();
}));
// Restore selected cards from a shared URL.
try { const shared = new URL(window.location.href).searchParams.get('gpus'); if(shared) { shared.split('|').forEach(n => { const tr=catalogRows.find(t => gpuName(t)===n); if(tr) addGpu(tr); }); } } catch(e) {}


/* ===== v7: поля в одну строку, перетаскивание строк, шкалы производительности ===== */
var perfReady=false;
const reduceMotion=matchMedia('(prefers-reduced-motion:reduce)').matches;

// 1. Подпись поля подбирается под его ширину — все поля помещаются в одну строку.
const PH={'filter-all':['Все видеокарты','Все'],'filter-nvidia':['NVIDIA — выбрать модель','NVIDIA — модель','NVIDIA'],'filter-amd':['AMD — выбрать модель','AMD — модель','AMD'],'filter-intel':['INTEL — выбрать модель','INTEL — модель','INTEL'],'filter-retro':['Ретро карты до 2015г','Ретро до 2015г','Ретро'],'filter-favorites':['★ Избранные','★ Избр.','★']};
const phCtx=document.createElement('canvas').getContext('2d');
function fitPlaceholders(){
  for(const [id,list] of Object.entries(PH)){
    const inp=document.getElementById(id), cs=getComputedStyle(inp);
    phCtx.font='800 '+cs.fontSize+' '+cs.fontFamily;
    const room=inp.clientWidth-parseFloat(cs.paddingLeft)-parseFloat(cs.paddingRight)-4;
    inp.placeholder=list.find(t=>phCtx.measureText(t).width<=room)||list[list.length-1];
  }
}
new ResizeObserver(fitPlaceholders).observe(document.querySelector('.filters'));
if(document.fonts)document.fonts.ready.then(fitPlaceholders);

// 2. Шкалы производительности.
// Демо-индекс: средний относительный FPS в играх (RTX 5090 = 100), значения ПРИБЛИЗИТЕЛЬНЫЕ.
// С базой данных достаточно выводить <tr data-perf="..."> — таблица GPU_PERF тогда не нужна.
const GPU_PERF={}; // производительность берётся из базы: <tr data-perf="Passmark G3D Mark">
const perfList=document.getElementById('perfList'), perfEmpty=document.getElementById('perfEmpty'), perfPanel=document.getElementById('perfPanel'), perfEls=new Map();
const fmtPct=v=>(v>=10?Math.round(v):v.toFixed(1).replace('.',','))+'%';
catalogRows.forEach(tr=>{
  tr.dataset.perf=tr.dataset.perf||GPU_PERF[gpuName(tr)]||0;
  const cell=tr.cells[1];
  cell.insertAdjacentHTML('beforeend','<div class="mini-perf"><span class="mini-track"><i class="mini-fill"></i></span><b>0%</b></div>');
  cell.querySelector('.gpu-name').insertAdjacentHTML('afterbegin','<span class="row-grip" title="Потяните, чтобы изменить порядок" aria-hidden="true"></span>');
});
// Шкала плавно вырастает/уменьшается (CSS transition), число меняется синхронно с ней.
function setBar(fill,label,pct,instant){
  cancelAnimationFrame(label._r);
  const from=instant?0:(+label.dataset.v||0);
  label.dataset.v=pct; fill.style.width=pct+'%';
  if(instant||reduceMotion){label.textContent=fmtPct(pct);return;}
  const t0=performance.now();
  const step=t=>{const k=Math.min(1,(t-t0)/800), e=1-Math.pow(1-k,3);label.textContent=fmtPct(from+(pct-from)*e);if(k<1)label._r=requestAnimationFrame(step);};
  label._r=requestAnimationFrame(step);
}
function renderPerf(){
  const sel=catalogRows.filter(tr=>tr.dataset.selected==='true').sort((a,b)=>b.dataset.perf-a.dataset.perf);
  const max=sel.length?+sel[0].dataset.perf:0, keep=new Set(sel.map(gpuName));
  perfEmpty.hidden=sel.length>0;
  const first=new Map([...perfEls].map(([k,el])=>[k,el.offsetTop]));
  for(const [k,el] of perfEls) if(!keep.has(k)){el.style.top=el.offsetTop+'px';el.classList.add('leaving');perfEls.delete(k);setTimeout(()=>el.remove(),300);}
  sel.forEach((tr,i)=>{
    const k=gpuName(tr); let el=perfEls.get(k);
    if(!el){
      el=document.createElement('div');el.className='perf-row entering';el.dataset.brand=tr.dataset.brand;
      el.innerHTML='<span class="perf-rank"></span><span class="perf-name"></span><span class="perf-track"><i class="perf-fill"></i></span><b class="perf-val">0%</b>';
      el.querySelector('.perf-name').textContent=k;perfEls.set(k,el);
    }
    perfList.appendChild(el);el.querySelector('.perf-rank').textContent=i+1;
  });
  catalogRows.forEach(tr=>{if(tr.dataset.selected!=='true')setBar(tr.querySelector('.mini-fill'),tr.querySelector('.mini-perf b'),0,true);});
  // FLIP: строки плавно «перетекают» на новые места
  if(!reduceMotion)for(const [k,el] of perfEls){const dy=first.has(k)?first.get(k)-el.offsetTop:0;if(dy){el.style.transition='none';el.style.transform='translateY('+dy+'px)';}}
  void perfList.offsetHeight;
  for(const el of perfEls.values()){el.style.transition='';el.style.transform='';el.classList.remove('entering');}
  sel.forEach(tr=>{
    const pct=max?tr.dataset.perf/max*100:0, el=perfEls.get(gpuName(tr));
    setBar(el.querySelector('.perf-fill'),el.querySelector('.perf-val'),pct);
    setBar(tr.querySelector('.mini-fill'),tr.querySelector('.mini-perf b'),pct);
  });
}


// 3. Перетаскивание строк мышью (за название; на сенсорных экранах — за значок ).
(function(){
  const tbody=table.tBodies[0];
  let d=null, raf=0;
  const rowsNow=()=>[...tbody.rows].filter(r=>r.dataset.selected==='true');
  const tTop=()=>table.getBoundingClientRect().top;
  const shift=(r,y,anim)=>{for(const c of r.cells){c.style.transition=anim?'transform .25s cubic-bezier(.22,.8,.3,1)':'none';c.style.transform=y?'translateY('+y+'px)':'';}};
  function place(){
    if(!d||!d.on)return;
    const rows=rowsNow(), others=rows.filter(r=>r!==d.tr);
    const want=d.cy-tTop()-d.off, mid=want+d.tr.offsetHeight/2;
    const target=others.filter(r=>mid>r.offsetTop+r.offsetHeight/2).length;
    if(target!==rows.indexOf(d.tr)){
      const was=new Map(others.map(r=>[r,r.offsetTop]));
      if(target>=others.length)tbody.appendChild(d.tr);else tbody.insertBefore(d.tr,others[target]);
      others.forEach(r=>{const dy=was.get(r)-r.offsetTop;if(dy)shift(r,dy,false);});
      void tbody.offsetHeight;
      others.forEach(r=>{if(r.cells[0].style.transform)shift(r,0,true);});
    }
    shift(d.tr,want-d.tr.offsetTop,false);
  }
  function loop(){
    if(!d||!d.on)return;
    const vr=viewport.getBoundingClientRect(), top=vr.top+(table.tHead?table.tHead.offsetHeight:0);
    const sp=d.cy<top+24?-8:d.cy>vr.bottom-36?8:0;
    if(sp){viewport.scrollTop+=sp;place();}
    raf=requestAnimationFrame(loop);
  }
  tbody.addEventListener('pointerdown',e=>{
    const cell=e.target.closest('td:nth-child(2)');
    if(!cell||e.button!==0||(e.pointerType!=='mouse'&&!e.target.closest('.row-grip')))return;
    const tr=cell.parentElement;
    d={tr,id:e.pointerId,y0:e.clientY,cy:e.clientY,off:e.clientY-tTop()-tr.offsetTop,on:false};
    cell.setPointerCapture(e.pointerId);
  });
  tbody.addEventListener('pointermove',e=>{
    if(!d||e.pointerId!==d.id)return;
    d.cy=e.clientY;
    if(!d.on){
      if(Math.abs(d.cy-d.y0)<5)return;
      d.on=true;window.rowDragging=true;
      d.tr.classList.add('row-dragging');document.body.classList.add('is-row-dragging');loop();
    }
    place();
  });
  const end=e=>{
    if(!d||e.pointerId!==d.id)return;
    const w=d;d=null;cancelAnimationFrame(raf);
    if(!w.on)return;
    window.rowDragging=false;document.body.classList.remove('is-row-dragging');
    shift(w.tr,0,true);
    setTimeout(()=>{w.tr.classList.remove('row-dragging');for(const c of w.tr.cells)c.style.transition='';},280);
  };
  tbody.addEventListener('pointerup',end);
  tbody.addEventListener('pointercancel',end);
})();

perfReady=true;renderPerf();

/* ===== v17: меню-«шторка» на планшетах и телефонах ===== */
(function(){
  const root=document.documentElement, btn=document.getElementById('menuToggle');
  const set=on=>{root.classList.toggle('menu-open',on);btn.setAttribute('aria-expanded',String(on));};
  btn.addEventListener('click',()=>set(!root.classList.contains('menu-open')));
  document.getElementById('drawerBackdrop').addEventListener('click',()=>set(false));
  document.querySelector('.sidebar').addEventListener('click',e=>{if(e.target.closest('.navitem,#siteFeedback,.help-link'))set(false);});
  document.addEventListener('keydown',e=>{if(e.key==='Escape')set(false);});
  matchMedia('(min-width:1151px)').addEventListener('change',e=>{if(e.matches)set(false);});
})();

/* ===== v18: ориентация таблицы (видеокарты в строках / в столбцах) ===== */
var tReady=false;
const gridT=document.getElementById('gridT'), tPanel=document.querySelector('.table-panel'), orientBtn=document.getElementById('orientToggle'), ORIENT_KEY='gpuCompareOrientV1';
function renderT(){
  const rows=[...table.tBodies[0].rows].filter(r=>r.dataset.selected==='true');
  const labels=[...table.tHead.rows[0].cells].slice(2), esc=escapeRateHtml;
  const max=rows.reduce((m,r)=>Math.max(m,+r.dataset.perf||0),0);
  let h='<div class="tg-row" role="row"><div class="tg-corner" role="columnheader"></div>';
  if(!rows.length)h+='<div class="tg-head t-empty" role="columnheader">Выберите видеокарты в полях выше</div>';
  rows.forEach(r=>{
    const n=gpuName(r), pct=max?(+r.dataset.perf||0)/max*100:0, fav=r.dataset.favorite==='true', sub=r.cells[1].querySelector('small');
    h+='<div class="tg-head" role="columnheader" data-name="'+esc(n)+'" data-brand="'+r.dataset.brand+'"><div class="t-acts"><span class="t-grip" title="Потяните, чтобы изменить порядок" aria-hidden="true"></span>'
     +'<button type="button" class="t-btn fav'+(fav?' is-favorite':'')+'" data-act="fav" title="'+(fav?'Убрать из избранного':'Добавить в избранное')+'" aria-label="Избранное: '+esc(n)+'">'+(fav?'★':'☆')+'</button>'
     +'<button type="button" class="t-btn rm" data-act="rm" title="Убрать из сравнения" aria-label="Убрать '+esc(n)+' из сравнения"><svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 10v6M14 10v6"/></svg></button></div>'
     +'<span class="t-name">'+esc(n)+'</span><small>'+esc(sub?sub.textContent:'')+'</small>'
     +'<div class="mini-perf"><span class="mini-track"><i class="mini-fill" style="width:'+pct+'%"></i></span><b>'+fmtPct(pct)+'</b></div></div>';
  });
  h+='</div>';
  labels.forEach((th,i)=>{
    h+='<div class="tg-row'+(i%2?' alt':'')+'" role="row"><div class="tg-label'+(th.classList.contains('nc')?' nc':'')+'" role="rowheader"'+(th.title?' title="'+esc(th.title)+'"':'')+'>'+th.innerHTML+'</div>';
    if(!rows.length)h+='<div class="tg-cell" role="cell"></div>';
    rows.forEach(r=>{h+='<div class="tg-cell" role="cell">'+r.cells[i+2].textContent.split(' / ').map(x=>esc(x).replace(/ /g,'\u00a0')).join(' / ')+'</div>';});
    h+='</div>';
  });
  gridT.style.setProperty('--n',Math.max(rows.length,1));
  gridT.innerHTML=h;
}
gridT.addEventListener('click',e=>{
  const b=e.target.closest('button[data-act]'); if(!b)return;
 <script src="assets/main.js"></script>