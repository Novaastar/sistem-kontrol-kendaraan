<?php
$file = 'd:/laragon/www/sistem_kontrol_kendaraan_bank_jatim/flutter_driver/lib/main.dart';
$content = file_get_contents($file);
$content = preg_replace('/openDrawer\(\);\s*},\s*\),\s*\)\s*\]/sm', "openDrawer();\r\n                      },\r\n                    ),\r\n                  ),\r\n                  const SizedBox(width: 16),\r\n                  Image.asset('assets/img/logo.png', width: 200, color: Colors.white, fit: BoxFit.contain),\r\n                ]", $content);
file_put_contents($file, $content);
echo "Done";
