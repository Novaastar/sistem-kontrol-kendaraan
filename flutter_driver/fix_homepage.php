<?php
$file = 'd:/laragon/www/sistem_kontrol_kendaraan_bank_jatim/flutter_driver/lib/main.dart';
$content = file_get_contents($file);

$startStr = "@override Widget build(BuildContext context)=>Scaffold(";
$startPos = strpos($content, $startStr);

if (preg_match('/^\s*const SizedBox\(height: 24\),/m', $content, $matches, PREG_OFFSET_CAPTURE, $startPos)) {
    $endPos = $matches[0][1];
    $endLen = strlen($matches[0][0]);
    
    $replace = "@override Widget build(BuildContext context)=>Scaffold(\r\n    body:loading?const Center(child:CircularProgressIndicator()):RefreshIndicator(onRefresh:load,child:ListView(\r\n      padding: const EdgeInsets.only(top: 24, bottom: 24),\r\n      children:[\r\n        Padding(\r\n          padding: const EdgeInsets.symmetric(horizontal: 24),\r\n          child: Row(\r\n            children: [\r\n              CircleAvatar(\r\n                radius: 30,\r\n                backgroundColor: primaryRed.withOpacity(0.1),\r\n                backgroundImage: widget.foto != null && widget.foto!.isNotEmpty ? NetworkImage('\$apiBase/../uploads/profiles/\${widget.foto}') : null,\r\n                child: widget.foto == null || widget.foto!.isEmpty \r\n                  ? Text(widget.nama.substring(0, 2).toUpperCase(), style: const TextStyle(color: primaryRed, fontSize: 24, fontWeight: FontWeight.bold))\r\n                  : null,\r\n              ),\r\n              const SizedBox(width: 16),\r\n              Expanded(\r\n                child: Column(\r\n                  crossAxisAlignment: CrossAxisAlignment.start,\r\n                  children: [\r\n                    const Text('Selamat pagi,', style: TextStyle(color: Colors.black54, fontSize: 14)),\r\n                    Text(widget.nama.toUpperCase(), style: const TextStyle(color: Colors.black87, fontSize: 18, fontWeight: FontWeight.bold)),\r\n                    Text('NIP \${widget.nip}', style: const TextStyle(color: Colors.black54, fontSize: 14)),\r\n                  ],\r\n                ),\r\n              )\r\n            ],\r\n          ),\r\n        ),\r\n        const SizedBox(height: 24),";

    $content = substr_replace($content, $replace, $startPos, $endPos - $startPos + $endLen);
    file_put_contents($file, $content);
    echo "Done";
} else {
    echo "End not found";
}
