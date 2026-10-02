import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:intl/intl.dart';
import 'package:image_picker/image_picker.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:timezone/data/latest.dart' as tz;
import 'package:timezone/timezone.dart' as tz;
import 'package:flutter/foundation.dart' show kIsWeb;

// Web (Edge/Chrome): pakai localhost | Android: pakai IP lokal Wi-Fi PC
const String apiBase = kIsWeb
    ? 'http://localhost/sistem_kontrol_kendaraan_bank_jatim/api'
    : 'http://192.168.18.27/sistem_kontrol_kendaraan_bank_jatim/api';
const Color primaryRed = Color(0xffa91f2b);

PageRouteBuilder fadeRoute(Widget page) {
  return PageRouteBuilder(
    pageBuilder: (context, animation, secondaryAnimation) => page,
    transitionsBuilder: (context, animation, secondaryAnimation, child) {
      return FadeTransition(opacity: animation, child: child);
    },
    transitionDuration: const Duration(milliseconds: 250),
  );
}

final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  tz.initializeTimeZones();
  tz.setLocalLocation(tz.getLocation('Asia/Jakarta'));
  await NotifHelper.init();
  runApp(const BankJatimDriverApp());
}

void showCustomToast(BuildContext context, String title, String message) {
  ScaffoldMessenger.of(context).showSnackBar(SnackBar(
    content: Row(
      children: [
        Image.asset('assets/img/logo.png', height: 24),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(title, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black87, fontSize: 14)),
              Text(message, style: const TextStyle(color: Colors.black54, fontSize: 13)),
            ],
          ),
        ),
      ],
    ),
    backgroundColor: const Color(0xfffae6e8),
    behavior: SnackBarBehavior.floating,
    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12), side: const BorderSide(color: Color(0xfff3d8dc), width: 1)),
    margin: const EdgeInsets.all(16),
    elevation: 0,
    duration: const Duration(seconds: 5),
  ));
}

class Api {
  static Future<Map<String,dynamic>> login(String u,String p) async {
    final r=await http.post(Uri.parse('$apiBase/login.php'),headers:{'Content-Type':'application/json'},body:jsonEncode({'username':u,'password':p}));
    return jsonDecode(r.body);
  }
  static Future<List<dynamic>> tasks(String token) async {
    final r=await http.get(Uri.parse('$apiBase/penugasan.php'),headers:{'Authorization':'Bearer $token'});
    final d=jsonDecode(r.body); if(d['success']!=true) throw Exception(d['message']??'Gagal mengambil data'); return d['data'];
  }
  static Future<Map<String,dynamic>> update(String token,Map<String,dynamic> data) async {
    final r=await http.post(Uri.parse('$apiBase/update_perjalanan.php'),headers:{'Content-Type':'application/json','Authorization':'Bearer $token'},body:jsonEncode(data));
    return jsonDecode(r.body);
  }
  static Future<Map<String,dynamic>> profil(String token) async {
    final r=await http.get(Uri.parse('$apiBase/profil.php'),headers:{'Authorization':'Bearer $token'});
    final d=jsonDecode(r.body); if(d['success']!=true) throw Exception(d['message']??'Gagal mengambil data'); return d['data'];
  }
  static Future<Map<String,dynamic>> ubahPassword(String token, String oldPassword, String newPassword) async {
    final r=await http.post(Uri.parse('$apiBase/ubah_password.php'),headers:{'Content-Type':'application/json','Authorization':'Bearer $token'},body:jsonEncode({'password_lama':oldPassword,'password_baru':newPassword}));
    return jsonDecode(r.body);
  }
  static Future<Map<String,dynamic>> uploadFoto(String token, XFile file) async {
    final request = http.MultipartRequest('POST', Uri.parse('$apiBase/upload_foto.php'));
    request.headers['Authorization'] = 'Bearer $token';
    final bytes = await file.readAsBytes();
    request.files.add(http.MultipartFile.fromBytes('foto', bytes, filename: file.name));
    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    return jsonDecode(response.body);
  }
}

// ─────────────────────────────────────────────
//  SPLASH SCREEN
// ─────────────────────────────────────────────
class SplashPage extends StatefulWidget {
  const SplashPage({super.key});
  @override State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage> with TickerProviderStateMixin {
  late AnimationController _logoCtrl;
  late AnimationController _ornCtrl;
  late Animation<double> _logoOpacity;
  late Animation<Offset> _ornTopRight;
  late Animation<Offset> _ornBottomLeft;
  late Animation<double> _textOpacity;
  late Animation<Offset> _textSlide;

  @override
  void initState() {
    super.initState();

    _ornCtrl = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 750),
    );
    _ornTopRight = Tween<Offset>(
      begin: const Offset(0.6, 0),
      end: Offset.zero,
    ).animate(CurvedAnimation(parent: _ornCtrl, curve: Curves.easeOut));
    _ornBottomLeft = Tween<Offset>(
      begin: const Offset(-0.6, 0),
      end: Offset.zero,
    ).animate(CurvedAnimation(parent: _ornCtrl, curve: Curves.easeOut));

    _logoCtrl = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 900),
    );
    _logoOpacity = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(parent: _logoCtrl, curve: Curves.easeIn),
    );
    
    _textOpacity = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(parent: _logoCtrl, curve: const Interval(0.4, 1.0, curve: Curves.easeIn)),
    );
    _textSlide = Tween<Offset>(begin: const Offset(0, 0.5), end: Offset.zero).animate(
      CurvedAnimation(parent: _logoCtrl, curve: const Interval(0.4, 1.0, curve: Curves.easeOutQuart)),
    );

    _ornCtrl.forward();
    Future.delayed(const Duration(milliseconds: 300), () {
      if (mounted) _logoCtrl.forward();
    });

    Future.delayed(const Duration(milliseconds: 2500), () async {
      if (mounted) {
        final prefs = await SharedPreferences.getInstance();
        final token = prefs.getString('token');
        if (token != null && token.isNotEmpty) {
          final nama = prefs.getString('nama') ?? 'User';
          final nip = prefs.getString('nip') ?? '-';
          final foto = prefs.getString('foto');
          Navigator.of(context).pushReplacement(
            PageRouteBuilder(
              transitionDuration: const Duration(milliseconds: 400),
              pageBuilder: (_, __, ___) => MainNavigation(token: token, nama: nama, nip: nip, foto: foto),
              transitionsBuilder: (_, anim, __, child) {
                return SlideTransition(
                  position: Tween<Offset>(begin: const Offset(0, 0.1), end: Offset.zero).animate(CurvedAnimation(parent: anim, curve: Curves.easeOutQuart)),
                  child: FadeTransition(opacity: anim, child: child),
                );
              },
            ),
          );
        } else {
          Navigator.of(context).pushReplacement(
            PageRouteBuilder(
              transitionDuration: const Duration(milliseconds: 400),
              pageBuilder: (_, __, ___) => const LoginPage(),
              transitionsBuilder: (_, anim, __, child) {
                return SlideTransition(
                  position: Tween<Offset>(begin: const Offset(0, 0.1), end: Offset.zero).animate(CurvedAnimation(parent: anim, curve: Curves.easeOutQuart)),
                  child: FadeTransition(opacity: anim, child: child),
                );
              },
            ),
          );
        }
      }
    });
  }

  @override
  void dispose() {
    _logoCtrl.dispose();
    _ornCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          Positioned(
            top: 0, right: 0,
            width: 160, height: 160,
            child: SlideTransition(
              position: _ornTopRight,
              child: const CustomPaint(painter: const _OrnamentPainter(flip: false)),
            ),
          ),
          Center(
            child: FadeTransition(
              opacity: _logoOpacity,
              child: Image.asset(
                'assets/img/logo.png',
                width: 200,
              ),
            ),
          ),
          Positioned(
            bottom: 0, left: 0,
            width: 160, height: 160,
            child: SlideTransition(
              position: _ornBottomLeft,
              child: const CustomPaint(painter: const _OrnamentPainter(flip: true)),
            ),
          ),
          Align(
            alignment: Alignment.bottomCenter,
            child: Padding(
              padding: const EdgeInsets.only(bottom: 30),
              child: SlideTransition(
                position: _textSlide,
                child: FadeTransition(
                  opacity: _textOpacity,
                  child: const Text(
                    'Powered by Bank Jatim',
                    style: TextStyle(
                      color: Colors.black54,
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 1.2,
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

/// Melukis ornamen motif batik / ukiran sudut.
/// [flip] = true → pojok kiri bawah (dicermin 180°)
///        = false → pojok kanan atas (normal)
class _OrnamentPainter extends CustomPainter {
  final bool flip;
  const _OrnamentPainter({required this.flip});

  @override
  void paint(Canvas canvas, Size size) {
    if (flip) {
      // Cermin 180° dari titik tengah agar ornamen simetris pojok kiri bawah
      canvas.translate(size.width, size.height);
      canvas.rotate(3.14159265358979);
    }

    final gold   = Paint()..color = const Color(0xFFB8960C).withValues(alpha: 0.88)..style = PaintingStyle.stroke..strokeWidth = 2.0..strokeCap = StrokeCap.round;
    final goldFill = Paint()..color = const Color(0xFFB8960C).withValues(alpha: 0.25)..style = PaintingStyle.fill;
    final green  = Paint()..color = const Color(0xFF6B7C2D).withValues(alpha: 0.80)..style = PaintingStyle.stroke..strokeWidth = 2.2..strokeCap = StrokeCap.round;
    final green2 = Paint()..color = const Color(0xFF6B7C2D).withValues(alpha: 0.55)..style = PaintingStyle.stroke..strokeWidth = 1.5..strokeCap = StrokeCap.round;
    final red    = Paint()..color = const Color(0xFFC0392B).withValues(alpha: 0.55)..style = PaintingStyle.stroke..strokeWidth = 1.3..strokeCap = StrokeCap.round;
    final redFill = Paint()..color = const Color(0xFFC0392B).withValues(alpha: 0.38)..style = PaintingStyle.fill;

    // ── Kotak ukiran besar sudut kanan atas ──
    final outerBox = RRect.fromRectAndRadius(
      const Rect.fromLTWH(108, 8, 38, 38), const Radius.circular(4));
    final innerBox = RRect.fromRectAndRadius(
      const Rect.fromLTWH(115, 15, 24, 24), const Radius.circular(3));
    canvas.drawRRect(outerBox, gold);
    canvas.drawRRect(innerBox, gold..strokeWidth = 1.4);
    canvas.drawRect(const Rect.fromLTWH(121, 21, 12, 12), goldFill);
    // garis silang kotak
    canvas.drawLine(const Offset(108, 27), const Offset(146, 27), gold..strokeWidth = 0.8..color = const Color(0xFFB8960C).withValues(alpha: 0.4));
    canvas.drawLine(const Offset(127, 8),  const Offset(127, 46), gold..strokeWidth = 0.8..color = const Color(0xFFB8960C).withValues(alpha: 0.4));

    // ── Sulur lengkung utama dari sudut kanan atas ke bawah ──
    final path1 = Path()
      ..moveTo(148, 2)
      ..cubicTo(148, 2, 130, 6, 118, 18)
      ..cubicTo(118, 18, 106, 30, 104, 52);
    canvas.drawPath(path1, green..strokeWidth = 2.2..color = const Color(0xFF6B7C2D).withValues(alpha: 0.80));

    final path2 = Path()
      ..moveTo(158, 10)
      ..cubicTo(158, 10, 136, 14, 122, 30)
      ..cubicTo(122, 30, 108, 46, 106, 70);
    canvas.drawPath(path2, green2..strokeWidth = 1.6);

    // percabangan atas
    canvas.drawPath(Path()..moveTo(118,18)..cubicTo(118,18,112,13,108,7), green2..strokeWidth = 1.3);
    canvas.drawPath(Path()..moveTo(122,30)..cubicTo(122,30,115,22,110,13), green2..strokeWidth = 1.1);

    // titik ujung
    canvas.drawCircle(const Offset(108, 7),  2.8, goldFill..color = const Color(0xFFB8960C).withValues(alpha: 0.55));
    canvas.drawCircle(const Offset(110, 13), 2.0, goldFill..color = const Color(0xFF6B7C2D).withValues(alpha: 0.48));

    // ── Lingkaran beruntai (medali ornamen) ──
    canvas.drawCircle(const Offset(130, 58), 7,   gold..strokeWidth = 1.8..color = const Color(0xFFB8960C).withValues(alpha: 0.88));
    canvas.drawCircle(const Offset(130, 58), 3.5, goldFill..color = const Color(0xFFB8960C).withValues(alpha: 0.28));
    canvas.drawCircle(const Offset(147, 73), 5,   gold..strokeWidth = 1.5);
    canvas.drawCircle(const Offset(147, 73), 2.5, goldFill..color = const Color(0xFFB8960C).withValues(alpha: 0.22));
    final connPath = Path()..moveTo(130, 65)..quadraticBezierTo(138, 68, 147, 67);
    canvas.drawPath(connPath, gold..strokeWidth = 1.2);

    // ── Sulur vertikal turun ──
    final downPath = Path()
      ..moveTo(104, 52)
      ..cubicTo(104, 52, 100, 76, 102, 100)
      ..cubicTo(102, 100, 104, 122, 112, 140);
    canvas.drawPath(downPath, green..strokeWidth = 1.8..color = const Color(0xFF6B7C2D).withValues(alpha: 0.58));

    final downPath2 = Path()..moveTo(106,70)..cubicTo(106,70,103,90,105,112);
    canvas.drawPath(downPath2, green2..strokeWidth = 1.2);

    // percabangan kiri
    canvas.drawPath(Path()..moveTo(102,80)..quadraticBezierTo(95,76,90,70), green2..strokeWidth = 1.1);
    canvas.drawCircle(const Offset(90, 70), 2.4, goldFill..color = const Color(0xFFB8960C).withValues(alpha: 0.42));
    canvas.drawPath(Path()..moveTo(103,97)..quadraticBezierTo(96,94,92,88), green2..strokeWidth = 1.0);

    // ── Kotak ukiran kecil bawah ──
    canvas.drawRRect(
      RRect.fromRectAndRadius(const Rect.fromLTWH(110, 118, 30, 30), const Radius.circular(3)),
      gold..strokeWidth = 1.8..color = const Color(0xFFB8960C).withValues(alpha: 0.88));
    canvas.drawRRect(
      RRect.fromRectAndRadius(const Rect.fromLTWH(116, 124, 18, 18), const Radius.circular(2)),
      gold..strokeWidth = 1.2);
    canvas.drawRect(const Rect.fromLTWH(121, 129, 8, 8), goldFill..color = const Color(0xFFB8960C).withValues(alpha: 0.22));
    canvas.drawLine(const Offset(110,133), const Offset(140,133), gold..strokeWidth = 0.7..color = const Color(0xFFB8960C).withValues(alpha: 0.4));
    canvas.drawLine(const Offset(125,118), const Offset(125,148), gold..strokeWidth = 0.7..color = const Color(0xFFB8960C).withValues(alpha: 0.4));

    // ── Aksen merah ──
    canvas.drawCircle(const Offset(150, 48),  4,   red..strokeWidth = 1.4);
    canvas.drawCircle(const Offset(150, 48),  1.8, redFill);
    canvas.drawCircle(const Offset(156, 100), 3.5, red..strokeWidth = 1.2);
    canvas.drawCircle(const Offset(156, 100), 1.5, redFill);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

// ─────────────────────────────────────────────
//  APP ROOT
// ─────────────────────────────────────────────
class BankJatimDriverApp extends StatelessWidget {
  const BankJatimDriverApp({super.key});
  @override Widget build(BuildContext c)=>MaterialApp(
    title:'Bank Jatim Driver',
    debugShowCheckedModeBanner:false,
    theme:ThemeData(
      colorScheme:ColorScheme.fromSeed(seedColor:primaryRed),
      useMaterial3:true,
      scaffoldBackgroundColor: const Color(0xfff5f7fa),
      fontFamily: 'Inter',
    ),
    navigatorKey: navigatorKey,
    home:const SplashPage(),
  );
}

@pragma('vm:entry-point')
void notificationTapBackground(NotificationResponse notificationResponse) {
  debugPrint('[NOTIFICATION] Background/Terminated tap detected. Payload: ${notificationResponse.payload}');
  // Notifikasi ditekan saat aplikasi mati penuh.
  // Jika app mati, onDidReceiveNotificationResponse akan dipanggil ulang saat UI sudah siap (tergantung OS),
  // atau kita hanya butuh ini agar sistem tidak crash saat mencari handler.
}

// ─────────────────────────────────────────────
//  NOTIF HELPER
// ─────────────────────────────────────────────
class NotifHelper {
  static final FlutterLocalNotificationsPlugin _notifPlugin = FlutterLocalNotificationsPlugin();

  static Future<void> init() async {
    if (kIsWeb) return; // Notifikasi lokal (custom sound & schedule) tidak disupport penuh di Web
    const AndroidInitializationSettings androidSettings = AndroidInitializationSettings('@mipmap/ic_launcher');
    const InitializationSettings initSettings = InitializationSettings(android: androidSettings);
    await _notifPlugin.initialize(
      initSettings,
      onDidReceiveNotificationResponse: (NotificationResponse response) async {
        final payload = response.payload;
        debugPrint('[NOTIFICATION] Foreground/Background tap detected. Payload: $payload');
        if (payload != null && (payload == 'km_awal' || payload == 'km_akhir')) {
          _handleNotificationClick(payload);
        }
      },
      onDidReceiveBackgroundNotificationResponse: notificationTapBackground,
    );

    // Minta izin notifikasi dan alarm untuk Android 13+ (API 33+)
    final androidPlugin = _notifPlugin.resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>();
    if (androidPlugin != null) {
      await androidPlugin.requestNotificationsPermission();
      await androidPlugin.requestExactAlarmsPermission();
    }
  }

  static Future<void> _handleNotificationClick(String payload) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    if (token == null || token.isEmpty) return;

    try {
      final tasks = await Api.tasks(token);
      final context = navigatorKey.currentContext;

      if (payload == 'km_awal') {
        // Cari tugas yang statusnya 'ditugaskan' (belum isi km awal)
        final targetTasks = tasks.where((x) => x['status'] == 'ditugaskan').toList();
        if (targetTasks.isNotEmpty) {
          if (context != null) {
            Navigator.push(context, fadeRoute(InputPerjalananPage(token: token, item: targetTasks.first)));
          }
        } else if (context != null) {
          showCustomToast(context, 'Info', 'Tugas KM Awal sudah diselesaikan atau tidak tersedia.');
        }

      } else if (payload == 'km_akhir') {
        // Cek dulu apakah masih ada tugas 'ditugaskan' (km awal belum diisi)
        final belumKmAwal = tasks.where((x) => x['status'] == 'ditugaskan').toList();
        if (belumKmAwal.isNotEmpty) {
          // Driver belum mengisi km awal, tampilkan peringatan
          if (context != null) {
            showCustomToast(context, 'Perhatian', 'Anda belum menginputkan KM Awal. Silakan isi KM Awal terlebih dahulu.');
          }
          return;
        }

        // Jika km awal sudah diisi, cari tugas 'berjalan' untuk km akhir
        final targetTasks = tasks.where((x) => x['status'] == 'berjalan').toList();
        if (targetTasks.isNotEmpty) {
          if (context != null) {
            Navigator.push(context, fadeRoute(InputPerjalananPage(token: token, item: targetTasks.first)));
          }
        } else if (context != null) {
          showCustomToast(context, 'Info', 'Tugas KM Akhir sudah diselesaikan atau tidak tersedia.');
        }

      } else {
        final targetTasks = tasks.where((x) => x['status'] != 'selesai').toList();
        if (targetTasks.isNotEmpty && context != null) {
          Navigator.push(context, fadeRoute(InputPerjalananPage(token: token, item: targetTasks.first)));
        } else if (context != null) {
          showCustomToast(context, 'Info', 'Tidak ada tugas aktif saat ini.');
        }
      }
    } catch (e) {
      debugPrint('Error handling notif click: $e');
    }
  }

  static Future<void> fetchAndSchedule(String token) async {
    if (kIsWeb) return; // Hindari error timezone/scheduling di Edge/Chrome Web
    try {
      final tasks = await Api.tasks(token);
      
      // Cek kebutuhan pengisian
      final needKmAwal  = tasks.any((x) => x['status'] == 'ditugaskan');
      final needKmAkhir = tasks.any((x) => x['status'] == 'berjalan');
      
      // Driver tidak punya penugasan aktif sama sekali
      final tidakAdaTugas = tasks.isEmpty || tasks.every((x) => x['status'] == 'selesai');

      final response = await http.get(
        Uri.parse('$apiBase/notif_settings.php'),
        headers: {'Authorization': 'Bearer $token'}
      );
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        debugPrint('[NOTIFICATION] Membatalkan jadwal lama...');
        await _notifPlugin.cancelAll(); // Reset jadwal lama
        debugPrint('[NOTIFICATION] Schedule Cancelled: Semua jadwal lama dibatalkan.');
        
        final pagi = data['data']['pagi'];
        final sore = data['data']['sore'];

        if (pagi['aktif'] == true) {
          if (needKmAwal) {
            // Ada penugasan → ingatkan isi KM Awal
            _scheduleDaily(1, 'Reminder Pagi 🌅', 'Selamat pagi! Silakan lakukan input KM Awal dan Jam Ambil Kendaraan hari ini.', pagi['jam'], pagi['menit'], 'km_awal');
          } else if (tidakAdaTugas) {
            // Tidak ada penugasan → notif istirahat pagi
            _scheduleDaily(1, 'Selamat Pagi! ☀️', 'Hari ini Anda tidak ada penugasan. Selamat beristirahat dan menikmati waktu Anda! Tetap pantau aplikasi ya, siapa tahu ada penugasan mendadak 😊', pagi['jam'], pagi['menit'], 'info');
          }
        }

        if (sore['aktif'] == true) {
          if (needKmAkhir) {
            // Ada perjalanan aktif → ingatkan isi KM Akhir
            _scheduleDaily(2, 'Reminder Sore 🌆', 'Jangan lupa lakukan input KM Akhir dan Jam Pulang Kendaraan hari ini.', sore['jam'], sore['menit'], 'km_akhir');
          } else if (tidakAdaTugas) {
            // Tidak ada penugasan → notif istirahat sore
            _scheduleDaily(2, 'Pengingat Sore 🌙', 'Tidak ada penugasan hari ini. Jangan lupa beristirahat, laksanakan ibadah, dan makan ya! Tetap pantau aplikasi siapa tahu ada penugasan mendadak 🙏', sore['jam'], sore['menit'], 'info');
          }
        }
      }
    } catch (e) {
      debugPrint('[NOTIFICATION ERROR] fetch/schedule notif: $e');
    }
  }

  static Future<void> _scheduleDaily(int id, String title, String body, int hour, int minute, String payload) async {
    final now = tz.TZDateTime.now(tz.local);
    var scheduledDate = tz.TZDateTime(tz.local, now.year, now.month, now.day, hour, minute);
    if (scheduledDate.isBefore(now)) {
      scheduledDate = scheduledDate.add(const Duration(days: 1));
    }
    
    // Konfigurasi notifikasi + custom sound
    const androidDetails = AndroidNotificationDetails(
      'reminder_km_channel_v4', // channel konsisten dengan file audio custom
      'Reminder KM',
      channelDescription: 'Reminder pengisian KM awal dan akhir',
      importance: Importance.max,
      priority: Priority.high,
      sound: RawResourceAndroidNotificationSound('custom_suara'), // Nama file-nya 'custom_suara'
    );
    const details = NotificationDetails(android: androidDetails);

    await _notifPlugin.zonedSchedule(
      id,
      title,
      body,
      scheduledDate,
      details,
      androidScheduleMode: AndroidScheduleMode.inexactAllowWhileIdle, // Inexact untuk kompatibilitas & stabilitas Android Doze
      uiLocalNotificationDateInterpretation: UILocalNotificationDateInterpretation.absoluteTime,
      matchDateTimeComponents: DateTimeComponents.time, // Ulangi setiap hari di jam yang sama
      payload: payload,
    );
    
    debugPrint('[NOTIFICATION]');
    debugPrint('Current Time: ${now.toString()}');
    debugPrint('Timezone: Asia/Jakarta');
    debugPrint('Notification ID: $id');
    debugPrint('Notification Type: $payload');
    debugPrint('Scheduled Time: ${scheduledDate.toString()}');
    debugPrint('Schedule Mode: inexactAllowWhileIdle');
    debugPrint('Schedule Created: true');
  }
}


class LoginPage extends StatefulWidget{const LoginPage({super.key});@override State<LoginPage> createState()=>_LoginPageState();}
class _LoginPageState extends State<LoginPage>{
  final u=TextEditingController(),p=TextEditingController();
  bool loading=false;
  bool _obscure=true;
  Future<void> submit() async {
    setState(()=>loading=true);
    try{
      final d=await Api.login(u.text,p.text);
      if(d['success']==true){
        final s=await SharedPreferences.getInstance();
        await s.setString('token',d['token']);
        await s.setString('nama',d['user']['nama']);
        await s.setString('nip',d['user']['username'] ?? '-');
        await s.setString('foto',d['user']['foto'] ?? '');
        if(mounted)Navigator.pushReplacement(context,fadeRoute(MainNavigation(token:d['token'],nama:d['user']['nama'],nip:d['user']['username'] ?? '-', foto:d['user']['foto'])));
      } else {
        throw Exception(d['message']);
      }
    } catch(e) {
      if(mounted)ScaffoldMessenger.of(context).showSnackBar(SnackBar(content:Text('$e')));
    } finally {
      if(mounted)setState(()=>loading=false);
    }
  }
    @override Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          Container(
            height: MediaQuery.of(context).size.height * 0.50,
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [Color(0xffd32f2f), primaryRed],
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
              )
            ),
          ),
          SafeArea(
            child: Column(
              children: [
                Expanded(
                  flex: 2,
                  child: Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 20),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(24),
                            boxShadow: [
                              BoxShadow(color: Colors.black.withValues(alpha: 0.15), blurRadius: 15, offset: const Offset(0, 5))
                            ]
                          ),
                          child: Image.asset('assets/img/logo.png', height: 80),
                        ),
                        const SizedBox(height: 24),
                        const Text('Sistem Kontrol Kendaraan', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18, letterSpacing: 0.5)),
                        const SizedBox(height: 6),
                        const Text('Bank Jatim Cabang Tuban', style: TextStyle(color: Colors.white70, fontSize: 14)),
                      ],
                    ),
                  ),
                ),
                Expanded(
                  flex: 3,
                  child: Container(
                    width: double.infinity,
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.vertical(top: Radius.circular(32)),
                      boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 20, spreadRadius: 0)],
                    ),
                    child: SingleChildScrollView(
                      padding: const EdgeInsets.fromLTRB(32, 40, 32, 24),
                      child: ConstrainedBox(
                        constraints: const BoxConstraints(maxWidth: 400),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text('Selamat Datang 👋', style: TextStyle(fontSize: 26, fontWeight: FontWeight.bold, color: Colors.black87)),
                            const SizedBox(height: 8),
                            const Text('Silakan login dengan kredensial Anda untuk melanjutkan.', style: TextStyle(color: Colors.grey, fontSize: 14, height: 1.5)),
                            const SizedBox(height: 40),
                            TextField(
                              controller: u,
                              decoration: InputDecoration(
                                labelText: 'NIP / Username',
                                labelStyle: const TextStyle(color: Colors.black54),
                                prefixIcon: const Icon(Icons.person_outline, color: primaryRed),
                                border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Colors.grey.shade300)),
                                enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Colors.grey.shade300)),
                                focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: const BorderSide(color: primaryRed, width: 2)),
                                filled: true,
                                fillColor: const Color(0xfff8f9fa)
                              )
                            ),
                            const SizedBox(height: 20),
                            TextField(
                              controller: p,
                              obscureText: _obscure,
                              decoration: InputDecoration(
                                labelText: 'Password',
                                labelStyle: const TextStyle(color: Colors.black54),
                                prefixIcon: const Icon(Icons.lock_outline, color: primaryRed),
                                suffixIcon: IconButton(icon: Icon(_obscure ? Icons.visibility_off_outlined : Icons.visibility_outlined, color: Colors.grey), onPressed: () => setState(() => _obscure = !_obscure)),
                                border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Colors.grey.shade300)),
                                enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: Colors.grey.shade300)),
                                focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: const BorderSide(color: primaryRed, width: 2)),
                                filled: true,
                                fillColor: const Color(0xfff8f9fa)
                              )
                            ),
                            const SizedBox(height: 40),
                            SizedBox(
                              width: double.infinity,
                              height: 55,
                              child: FilledButton(
                                style: FilledButton.styleFrom(backgroundColor: primaryRed, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)), elevation: 2),
                                onPressed: loading ? null : submit,
                                child: loading ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) : const Text('Login', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, letterSpacing: 1))
                              )
                            ),
                            const SizedBox(height: 30),
                            const Center(child: Text('v1.0.0', style: TextStyle(color: Colors.grey, fontSize: 13))),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}


class MainNavigation extends StatefulWidget {
  final String token,nama,nip;
  final String? foto;
  const MainNavigation({super.key,required this.token,required this.nama,required this.nip, this.foto});
  @override State<MainNavigation> createState() => _MainNavigationState();
}
class _MainNavigationState extends State<MainNavigation> {
  int _currentIndex = 0;
  String? _foto;
  final GlobalKey<_HistoryPageState> _historyKey = GlobalKey<_HistoryPageState>();
  
  @override
  void initState() {
    super.initState();
    _foto = widget.foto;
    // Schedule notifications from server settings
    NotifHelper.fetchAndSchedule(widget.token);
    
    // Simulate morning notification for UI testing
    Future.delayed(const Duration(seconds: 2), () {
      if(mounted) showCustomToast(context, 'Kontrol Kendaraan', 'Selamat datang, jangan lupa cek notifikasi.');
    });
  }

  void _updateFoto(String newFoto) {
    setState(() => _foto = newFoto);
    SharedPreferences.getInstance().then((s) => s.setString('foto', newFoto));
  }

  @override Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryRed,
        foregroundColor: Colors.white,
        elevation: 0,
        shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(bottom: Radius.circular(24)),
        ),
        leading: Builder(
          builder: (context) => IconButton(
            icon: const Icon(Icons.menu, size: 28),
            onPressed: () => Scaffold.of(context).openDrawer(),
          ),
        ),
        title: Image.asset('assets/img/logo.png', width: 160, color: Colors.white, fit: BoxFit.contain),
        centerTitle: true,
      ),
      drawer: Drawer(
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            UserAccountsDrawerHeader(
              decoration: const BoxDecoration(color: primaryRed),
              accountName: Text(widget.nama.toUpperCase(), style: const TextStyle(fontWeight: FontWeight.bold)),
              accountEmail: Text('NIP ${widget.nip}'),
              currentAccountPicture: CircleAvatar(
                backgroundColor: Colors.white,
                backgroundImage: _foto != null && _foto!.isNotEmpty ? NetworkImage('$apiBase/../uploads/profiles/$_foto') : null,
                child: _foto == null || _foto!.isEmpty 
                  ? Text(widget.nama.substring(0, 2).toUpperCase(), style: const TextStyle(color: primaryRed, fontSize: 24, fontWeight: FontWeight.bold))
                  : null,
              ),
            ),
            ListTile(
              selected: _currentIndex == 0,
              selectedTileColor: primaryRed.withValues(alpha: 0.1),
              leading: Icon(Icons.home, color: _currentIndex == 0 ? primaryRed : Colors.grey),
              title: Text('Beranda', style: TextStyle(color: _currentIndex == 0 ? primaryRed : Colors.black87, fontWeight: _currentIndex == 0 ? FontWeight.bold : FontWeight.normal)),
              onTap: () {
                Navigator.pop(context);
                setState(() => _currentIndex = 0);
              },
            ),
            ListTile(
              selected: _currentIndex == 1,
              selectedTileColor: primaryRed.withValues(alpha: 0.1),
              leading: Icon(Icons.history, color: _currentIndex == 1 ? primaryRed : Colors.grey),
              title: Text('Riwayat Perjalanan', style: TextStyle(color: _currentIndex == 1 ? primaryRed : Colors.black87, fontWeight: _currentIndex == 1 ? FontWeight.bold : FontWeight.normal)),
              onTap: () {
                Navigator.pop(context);
                setState(() => _currentIndex = 1);
              },
            ),
            ListTile(
              selected: _currentIndex == 2,
              selectedTileColor: primaryRed.withValues(alpha: 0.1),
              leading: Icon(Icons.person, color: _currentIndex == 2 ? primaryRed : Colors.grey),
              title: Text('Profil Saya', style: TextStyle(color: _currentIndex == 2 ? primaryRed : Colors.black87, fontWeight: _currentIndex == 2 ? FontWeight.bold : FontWeight.normal)),
              onTap: () {
                Navigator.pop(context);
                setState(() => _currentIndex = 2);
              },
            ),
            const Divider(),
            ListTile(
              leading: const Icon(Icons.logout, color: Colors.red),
              title: const Text('Keluar', style: TextStyle(color: Colors.red)),
              onTap: () async {
                Navigator.pop(context);
                showDialog(
                  context: context,
                  builder: (c) => AlertDialog(
                    title: const Text('Konfirmasi'),
                    content: const Text('Apakah Anda yakin ingin keluar dari aplikasi?'),
                    actions: [
                      TextButton(onPressed: () => Navigator.pop(c), child: const Text('Batal', style: TextStyle(color: Colors.grey))),
                      FilledButton(
                        style: FilledButton.styleFrom(backgroundColor: primaryRed),
                        onPressed: () async {
                          Navigator.pop(c);
                          showDialog(
                            context: context,
                            barrierDismissible: false,
                            builder: (BuildContext loadingCtx) {
                              return Dialog(
                                backgroundColor: Colors.white,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                                child: const Padding(
                                  padding: EdgeInsets.symmetric(vertical: 32, horizontal: 24),
                                  child: Column(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      CircularProgressIndicator(color: primaryRed),
                                      SizedBox(height: 16),
                                      Text('Sedang keluar...', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.black87)),
                                    ],
                                  ),
                                ),
                              );
                            },
                          );
                          
                          await Future.delayed(const Duration(milliseconds: 1500));
                          final s = await SharedPreferences.getInstance();
                          await s.clear();
                          if (context.mounted) {
                            Navigator.pushAndRemoveUntil(context, fadeRoute(const LoginPage()), (r) => false);
                          }
                        }, 
                        child: const Text('Keluar')
                      )
                    ],
                  )
                );
              },
            ),
          ],
        ),
      ),
      body: AnimatedIndexedStack(
        index: _currentIndex,
        children: [
          HomePage(
            token: widget.token, 
            nama: widget.nama, 
            nip: widget.nip, 
            foto: _foto,
            onNavigate: (int index) {
              setState(() => _currentIndex = index);
            }
          ),
          HistoryPage(key: _historyKey, token: widget.token),
          ProfilePage(token: widget.token, nama: widget.nama, nip: widget.nip, foto: _foto, onFotoChanged: _updateFoto),
        ],
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (index) {
          setState(() => _currentIndex = index);
          // Auto-refresh riwayat saat tab Riwayat dibuka
          if (index == 1) {
            _historyKey.currentState?.load();
          }
        },
        selectedItemColor: primaryRed,
        unselectedItemColor: Colors.grey,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home), label: 'Beranda'),
          BottomNavigationBarItem(icon: Icon(Icons.history), label: 'Riwayat'),
          BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profil'),
        ],
      ),
    );
  }
}

class HomePage extends StatefulWidget{
  final String token,nama,nip;
  final String? foto;
  final Function(int)? onNavigate;
  const HomePage({super.key,required this.token,required this.nama,required this.nip, this.foto, this.onNavigate});
  @override State<HomePage> createState()=>_HomePageState();
}
class _HomePageState extends State<HomePage> with WidgetsBindingObserver {List<dynamic> data=[];bool loading=true;
 String _greeting() {
   final h = DateTime.now().hour;
   if (h >= 5 && h < 11) return 'Selamat pagi,';
   if (h >= 11 && h < 15) return 'Selamat siang,';
   if (h >= 15 && h < 18) return 'Selamat sore,';
   return 'Selamat malam,';
 }
 @override 
 void initState() {
   super.initState();
   WidgetsBinding.instance.addObserver(this);
   load();
 }

 @override
 void dispose() {
   WidgetsBinding.instance.removeObserver(this);
   super.dispose();
 }

 @override
 void didChangeAppLifecycleState(AppLifecycleState state) {
   if (state == AppLifecycleState.resumed) {
     load();
   }
 }
 Future<void> load() async{try{data=await Api.tasks(widget.token);}catch(e){if(mounted)ScaffoldMessenger.of(context).showSnackBar(SnackBar(content:Text('$e')));}finally{if(mounted)setState(()=>loading=false);}}
 @override Widget build(BuildContext context)=>Scaffold(
    backgroundColor: const Color(0xfff8f9fa),
    body:loading?const Center(child:CircularProgressIndicator()):RefreshIndicator(onRefresh:load,child:ListView(
      padding: const EdgeInsets.only(top: 24, bottom: 24),
      children:[
        Container(
          margin: const EdgeInsets.symmetric(horizontal: 24),
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            boxShadow: [
              BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 24, offset: const Offset(0, 8)),
            ],
            border: Border.all(color: Colors.grey.shade100)
          ),
          child: Row(
            children: [
              Container(
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  boxShadow: [
                    BoxShadow(color: primaryRed.withValues(alpha: 0.2), blurRadius: 10, offset: const Offset(0, 4)),
                  ],
                ),
                child: CircleAvatar(
                  radius: 32,
                  backgroundColor: primaryRed.withValues(alpha: 0.1),
                  backgroundImage: widget.foto != null && widget.foto!.isNotEmpty ? NetworkImage('$apiBase/../uploads/profiles/${widget.foto}') : null,
                  child: widget.foto == null || widget.foto!.isEmpty 
                    ? Text(widget.nama.substring(0, 2).toUpperCase(), style: const TextStyle(color: primaryRed, fontSize: 24, fontWeight: FontWeight.bold))
                    : null,
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(_greeting(), style: TextStyle(color: Colors.grey.shade600, fontSize: 13, fontWeight: FontWeight.w500)),
                    const SizedBox(height: 4),
                    Text(widget.nama.toUpperCase(), style: const TextStyle(color: Colors.black87, fontSize: 18, fontWeight: FontWeight.bold, letterSpacing: 0.5)),
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(color: primaryRed.withValues(alpha: 0.08), borderRadius: BorderRadius.circular(8)),
                      child: Text('NIP ${widget.nip}', style: const TextStyle(color: primaryRed, fontSize: 12, fontWeight: FontWeight.w700)),
                    ),
                  ],
                ),
              )
            ],
          ),
        ),
        const SizedBox(height: 16),
        const Padding(
          padding: EdgeInsets.symmetric(horizontal: 24),
          child: Divider(),
        ),
        const SizedBox(height: 16),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Mobil Saya', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.black87)),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: primaryRed.withValues(alpha: 0.1), 
                  borderRadius: BorderRadius.circular(20)
                ),
                child: Text('${data.where((x)=>x['status']!='selesai').length} Penugasan', style: const TextStyle(color: primaryRed, fontWeight: FontWeight.bold, fontSize: 13)),
              )
            ],
          ),
        ),
        const SizedBox(height: 16),
        if(data.where((x)=>x['status']!='selesai').isEmpty) 
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 32),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: primaryRed.withValues(alpha: 0.05),
                    shape: BoxShape.circle,
                  ),
                  child: Icon(Icons.assignment_turned_in_rounded, size: 64, color: primaryRed.withValues(alpha: 0.8)),
                ),
                const SizedBox(height: 24),
                const Text('Hore! Semua Selesai', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.black87)),
                const SizedBox(height: 8),
                const Text('Belum ada penugasan aktif saat ini.\nAnda bisa bersantai sejenak.', textAlign: TextAlign.center, style: TextStyle(color: Colors.black54, fontSize: 15, height: 1.4)),
              ],
            ),
          ),
       ...data.where((x)=>x['status']!='selesai').map((x)=>Padding(
         padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
         child: InkWell(
           onTap: () async {
             await Navigator.push(context, fadeRoute(InputPerjalananPage(token: widget.token, item: x)));
             load();
           },
           borderRadius: BorderRadius.circular(20),
           child: Container(
             decoration: BoxDecoration(
               color: Colors.white,
               borderRadius: BorderRadius.circular(20),
               boxShadow: [
                 BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 15, offset: const Offset(0, 5)),
               ],
               border: Border.all(color: Colors.grey.shade100)
             ),
             child: Padding(
               padding: const EdgeInsets.all(16),
               child: Row(
                 children: [
                   Container(
                     padding: const EdgeInsets.all(8),
                     decoration: BoxDecoration(color: const Color(0xfff8f9fa), borderRadius: BorderRadius.circular(12)),
                     child: x['foto'] != null && x['foto'].isNotEmpty ? Image.network('$apiBase/../uploads/kendaraan/${x['foto']}', height: 40, width: 60, fit: BoxFit.contain, errorBuilder: (c, e, s) => Image.asset('assets/img/car.png', height: 40, width: 60, fit: BoxFit.contain)) : Image.asset('assets/img/car.png', height: 40, width: 60, fit: BoxFit.contain),
                   ),
                   const SizedBox(width: 16),
                   Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                     Text(x['merk'], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.black87)),
                     const SizedBox(height: 4),
                     Text(x['nomor_polisi'], style: const TextStyle(color: Colors.black54, fontSize: 14, fontWeight: FontWeight.w500)),
                     const SizedBox(height: 8),
                     Row(
                       children: [
                         Icon(Icons.location_on_outlined, size: 14, color: primaryRed.withValues(alpha: 0.7)),
                         const SizedBox(width: 4),
                         Expanded(child: Text(x['tujuan'] ?? '-', style: const TextStyle(color: Colors.black54, fontSize: 12), maxLines: 1, overflow: TextOverflow.ellipsis)),
                       ],
                     ),
                   ])),
                   Container(
                     padding: const EdgeInsets.all(8),
                     decoration: BoxDecoration(color: primaryRed.withValues(alpha: 0.05), shape: BoxShape.circle),
                     child: const Icon(Icons.arrow_forward_ios, color: primaryRed, size: 16),
                   )
                 ],
               ),
             ),
           ),
         ),
       ))
     ]
   ))
 );
}

class InputPerjalananPage extends StatefulWidget {
  final String token;
  final dynamic item;
  const InputPerjalananPage({super.key, required this.token, required this.item});
  @override State<InputPerjalananPage> createState() => _InputPerjalananPageState();
}
class _InputPerjalananPageState extends State<InputPerjalananPage> {
  bool loading = false;
  final km = TextEditingController();
  final ket = TextEditingController();

  Future<void> simpan() async {
    setState(()=>loading=true);
    try{
      final status = widget.item['status'];
      final now = TimeOfDay.now();
      final jam = '${now.hour.toString().padLeft(2,'0')}:${now.minute.toString().padLeft(2,'0')}';
      
      Map<String,dynamic> payload = {'penugasan_id': widget.item['id']};
      if (status == 'ditugaskan') {
        payload['jam_ambil'] = jam;
        payload['km_awal'] = int.tryParse(km.text.replaceAll(RegExp(r'[^0-9]'), ''));
      } else if (status == 'berjalan') {
        payload['jam_pulang'] = jam;
        final kmAkhir = int.tryParse(km.text.replaceAll(RegExp(r'[^0-9]'), ''));
        final kmAwal = int.tryParse(widget.item['km_awal']?.toString() ?? '0');
        
        if (kmAkhir != null && kmAwal != null && kmAkhir < kmAwal) {
          if (mounted) {
            showCustomToast(context, 'Validasi Gagal', 'KM Akhir tidak boleh lebih kecil dari KM Awal ($kmAwal).');
          }
          setState(() => loading = false);
          return;
        }
        
        payload['km_akhir'] = kmAkhir;
        payload['keterangan'] = ket.text;
      }

      final r=await Api.update(widget.token,payload);
      if(r['success']!=true)throw Exception(r['message']);
      if(mounted){
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content:Text('Data berhasil disimpan')));
        Navigator.pop(context);
      }
    }catch(e){
      if(mounted)ScaffoldMessenger.of(context).showSnackBar(SnackBar(content:Text('$e')));
    }finally{
      if(mounted)setState(()=>loading=false);
    }
  }

  @override Widget build(BuildContext context) {
    final x = widget.item;
    final isMulai = x['status'] == 'ditugaskan';
    final title = isMulai ? 'KM Awal' : 'KM Akhir';
    
    return Scaffold(
      appBar: AppBar(
        backgroundColor: primaryRed,
        foregroundColor: Colors.white,
        title: const Text('Input Kendaraan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        leading: IconButton(icon: const Icon(Icons.arrow_back), onPressed: () => Navigator.pop(context)),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(24),
              color: Colors.white,
              child: Row(
                children: [
                   x['foto'] != null && x['foto'].isNotEmpty ? Image.network('$apiBase/../uploads/kendaraan/${x['foto']}', height: 60, width: 100, fit: BoxFit.contain, errorBuilder: (c, e, s) => Image.asset('assets/img/car.png', height: 60, width: 100, fit: BoxFit.contain)) : Image.asset('assets/img/car.png', height: 60, width: 100, fit: BoxFit.contain),
                  const SizedBox(width: 16),
                  Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(x['merk'], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
                    Text(x['nomor_polisi'], style: const TextStyle(color: Colors.black54, fontSize: 16)),
                  ])),
                ],
              ),
            ),
            const Divider(height: 1, thickness: 1, color: Color(0xffeeeeee)),
            Padding(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Tanggal', style: TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  TextField(
                    controller: TextEditingController(text: DateFormat('dd MMMM yyyy').format(DateTime.now())),
                    readOnly: true,
                    decoration: InputDecoration(prefixIcon: const Icon(Icons.calendar_today, size: 20), border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)), filled: true, fillColor: Colors.grey.shade100, contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12))
                  ),
                  const SizedBox(height: 20),
                  Text(isMulai ? 'Jam Ambil' : 'Jam Pulang', style: const TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  TextField(
                    controller: TextEditingController(text: TimeOfDay.now().format(context)),
                    readOnly: true,
                    decoration: InputDecoration(prefixIcon: const Icon(Icons.access_time, size: 20), border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)), filled: true, fillColor: Colors.grey.shade100, contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12))
                  ),
                  const SizedBox(height: 20),
                  Text(title, style: const TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  TextField(
                    controller: km,
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(prefixIcon: const Icon(Icons.pin, size: 20), border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)), contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12))
                  ),
                  if(!isMulai) ...[
                    const SizedBox(height: 20),
                    const Text('Keterangan (opsional)', style: TextStyle(fontWeight: FontWeight.bold)),
                    const SizedBox(height: 8),
                    TextField(
                      controller: ket,
                      maxLines: 3,
                      decoration: InputDecoration(hintText: 'Contoh: Dinas ke Kantor Pusat', border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)), contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12))
                    )
                  ],
                  const SizedBox(height: 40),
                  SizedBox(
                    width: double.infinity,
                    height: 55,
                    child: FilledButton(
                      style: FilledButton.styleFrom(backgroundColor: primaryRed, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
                      onPressed: loading || x['status'] == 'selesai' ? null : simpan,
                      child: loading ? const CircularProgressIndicator(color: Colors.white) : Text(x['status'] == 'selesai' ? 'Tugas Selesai' : 'Simpan', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold))
                    ),
                  ),
                ],
              ),
            )
          ],
        ),
      ),
    );
  }
}

class HistoryPage extends StatefulWidget {
  final String token;
  const HistoryPage({super.key, required this.token});
  @override State<HistoryPage> createState() => _HistoryPageState();
}
class _HistoryPageState extends State<HistoryPage> with WidgetsBindingObserver {
  List<dynamic> data = [];
  bool loading = true;
  
  @override 
  void initState() { 
    super.initState(); 
    WidgetsBinding.instance.addObserver(this);
    load(); 
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      load(); // Auto-refresh saat aplikasi dibuka kembali dari background
    }
  }

  Future<void> load() async {
    if (mounted) setState(() => loading = true);
    try { data = await Api.tasks(widget.token); }
    catch(e) { if(mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e'))); }
    finally { if(mounted) setState(() => loading = false); }
  }
  
  @override Widget build(BuildContext context) {
    final history = data.where((x) => x['status'] == 'selesai').toList();
    
    return Scaffold(
      body: loading 
        ? const Center(child: CircularProgressIndicator()) 
        : RefreshIndicator(
            onRefresh: load,
            child: history.isEmpty 
              ? ListView(children: const [Padding(padding: EdgeInsets.all(24), child: Center(child: Text('Belum ada riwayat perjalanan yang selesai.')))])
              : ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: history.length,
                  itemBuilder: (context, i) {
                    final x = history[i];
                    return Card(
                      margin: const EdgeInsets.only(bottom: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.start,
                              children: [
                                Text(DateFormat('dd MMM yyyy').format(DateTime.parse(x['tanggal'])), style: const TextStyle(fontWeight: FontWeight.bold, color: primaryRed)),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                  decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(12)),
                                  child: const Text('Selesai', style: TextStyle(color: Colors.green, fontSize: 12, fontWeight: FontWeight.bold)),
                                )
                              ],
                            ),
                            const Divider(height: 20),
                            Row(
                              children: [
                                x['foto'] != null && x['foto'].isNotEmpty ? Image.network('$apiBase/../uploads/kendaraan/${x['foto']}', height: 40, width: 60, fit: BoxFit.contain, errorBuilder: (c, e, s) => Image.asset('assets/img/car.png', height: 40, width: 60, fit: BoxFit.contain)) : Image.asset('assets/img/car.png', height: 40, width: 60, fit: BoxFit.contain),
                                const SizedBox(width: 12),
                                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                  Text(x['merk'], style: const TextStyle(fontWeight: FontWeight.bold)),
                                  Text(x['nomor_polisi'], style: const TextStyle(color: Colors.black54, fontSize: 13)),
                                ])),
                              ],
                            ),
                            const SizedBox(height: 16),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.start,
                              children: [
                                Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                  const Text('Keberangkatan', style: TextStyle(color: Colors.black54, fontSize: 12)),
                                  Text(x['jam_ambil'] ?? '-', style: const TextStyle(fontWeight: FontWeight.bold)),
                                  Text('KM ${x['km_awal'] ?? '-'}', style: const TextStyle(fontSize: 12)),
                                ]),
                                const Icon(Icons.arrow_forward_ios, size: 16, color: Colors.grey),
                                Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                                  const Text('Kepulangan', style: TextStyle(color: Colors.black54, fontSize: 12)),
                                  Text(x['jam_pulang'] ?? '-', style: const TextStyle(fontWeight: FontWeight.bold)),
                                  Text('KM ${x['km_akhir'] ?? '-'}', style: const TextStyle(fontSize: 12)),
                                ]),
                              ],
                            ),
                            if (x['keterangan'] != null && x['keterangan'].toString().isNotEmpty) ...[
                              const SizedBox(height: 12),
                              Container(
                                width: double.infinity,
                                padding: const EdgeInsets.all(12),
                                decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(8)),
                                child: Text('Keterangan:\n${x['keterangan']}', style: const TextStyle(fontSize: 13, color: Colors.black87)),
                              )
                            ]
                          ],
                        ),
                      )
                    );
                  },
                )
          )
    );
  }
}


class ProfilePage extends StatefulWidget {
  final String token, nama, nip;
  final String? foto;
  final Function(String) onFotoChanged;
  const ProfilePage({super.key, required this.token, required this.nama, required this.nip, this.foto, required this.onFotoChanged});
  @override State<ProfilePage> createState() => _ProfilePageState();
}

class _ProfilePageState extends State<ProfilePage> {
  bool uploading = false;

  Future<void> gantiFoto() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) {
      setState(() => uploading = true);
      try {
        final res = await Api.uploadFoto(widget.token, pickedFile);
        if (res['success'] == true) {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Foto profil berhasil diubah')));
            widget.onFotoChanged(res['foto']);
          }
        } else {
          throw Exception(res['message']);
        }
      } catch (e) {
        if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString())));
      } finally {
        if (mounted) setState(() => uploading = false);
      }
    }
  }

  @override Widget build(BuildContext context) {
    return Scaffold(
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            const SizedBox(height: 20),
            Stack(
              clipBehavior: Clip.none,
              children: [
                CircleAvatar(
                  radius: 50,
                  backgroundColor: primaryRed.withValues(alpha: 0.1),
                  backgroundImage: widget.foto != null && widget.foto!.isNotEmpty ? NetworkImage('$apiBase/../uploads/profiles/${widget.foto}') : null,
                  child: widget.foto == null || widget.foto!.isEmpty 
                    ? Text(widget.nama.substring(0, 2).toUpperCase(), style: const TextStyle(color: primaryRed, fontSize: 36, fontWeight: FontWeight.bold))
                    : null,
                ),
                Positioned(
                  bottom: -5,
                  right: -5,
                  child: uploading ? const CircularProgressIndicator() : InkWell(
                    onTap: gantiFoto,
                    child: Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: primaryRed,
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white, width: 2)
                      ),
                      child: const Icon(Icons.edit, color: Colors.white, size: 18),
                    ),
                  )
                )
              ],
            ),
            const SizedBox(height: 24),
            Text(widget.nama.toUpperCase(), style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            Text('NIP ${widget.nip}', style: const TextStyle(fontSize: 16, color: Colors.black54)),
            const SizedBox(height: 40),
            const Divider(),
            ListTile(
              leading: const Icon(Icons.person_outline, color: Colors.black87),
              title: const Text('Data Pribadi'),
              trailing: const Icon(Icons.chevron_right),
              onTap: () => Navigator.push(context, fadeRoute(DataPribadiPage(token: widget.token))),
            ),
            ListTile(
              leading: const Icon(Icons.lock_outline, color: Colors.black87),
              title: const Text('Ubah Password'),
              trailing: const Icon(Icons.chevron_right),
              onTap: () => Navigator.push(context, fadeRoute(UbahPasswordPage(token: widget.token))),
            ),
            ListTile(
              leading: const Icon(Icons.help_outline, color: Colors.black87),
              title: const Text('Pusat Bantuan'),
              trailing: const Icon(Icons.chevron_right),
              onTap: () => Navigator.push(context, fadeRoute(const PusatBantuanPage())),
            ),
            const Divider(),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              height: 55,
              child: OutlinedButton.icon(
                style: OutlinedButton.styleFrom(
                  foregroundColor: primaryRed,
                  side: const BorderSide(color: primaryRed, width: 2),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))
                ),
                icon: const Icon(Icons.logout),
                label: const Text('Keluar', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                onPressed: () async {
                  showDialog(
                    context: context,
                    builder: (c) => AlertDialog(
                      title: const Text('Konfirmasi'),
                      content: const Text('Apakah Anda yakin ingin keluar dari aplikasi?'),
                      actions: [
                        TextButton(onPressed: () => Navigator.pop(c), child: const Text('Batal', style: TextStyle(color: Colors.grey))),
                        FilledButton(
                          style: FilledButton.styleFrom(backgroundColor: primaryRed),
                          onPressed: () async {
                            Navigator.pop(c);
                            showDialog(
                              context: context,
                              barrierDismissible: false,
                              builder: (BuildContext loadingCtx) {
                                return Dialog(
                                  backgroundColor: Colors.white,
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                                  child: const Padding(
                                    padding: EdgeInsets.symmetric(vertical: 32, horizontal: 24),
                                    child: Column(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        CircularProgressIndicator(color: primaryRed),
                                        SizedBox(height: 16),
                                        Text('Sedang keluar...', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.black87)),
                                      ],
                                    ),
                                  ),
                                );
                              },
                            );
                            
                            await Future.delayed(const Duration(milliseconds: 1500));
                            final s = await SharedPreferences.getInstance();
                            await s.clear();
                            if (context.mounted) {
                              Navigator.pushAndRemoveUntil(context, fadeRoute(const LoginPage()), (r) => false);
                            }
                          }, 
                          child: const Text('Keluar')
                        )
                      ],
                    )
                  );
                },
              ),
            ),
            const SizedBox(height: 30),
            const Text('Bank Jatim Driver v1.0.0', style: TextStyle(color: Colors.grey, fontSize: 12))
          ],
        ),
      )
    );
  }
}

class DataPribadiPage extends StatefulWidget {
  final String token;
  const DataPribadiPage({super.key, required this.token});
  @override State<DataPribadiPage> createState() => _DataPribadiPageState();
}
class _DataPribadiPageState extends State<DataPribadiPage> {
  Map<String,dynamic>? data;
  bool loading = true;
  @override void initState() { super.initState(); load(); }
  Future<void> load() async {
    try { data = await Api.profil(widget.token); }
    catch(e) { if(mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e'))); }
    finally { if(mounted) setState(() => loading = false); }
  }
  @override Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Data Pribadi', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)), backgroundColor: primaryRed, foregroundColor: Colors.white, centerTitle: true),
      body: loading 
        ? const Center(child: CircularProgressIndicator()) 
        : (data == null 
            ? const Center(child: Text('Gagal memuat data')) 
            : ListView(
                padding: const EdgeInsets.all(24),
                children: [
                  _buildItem('Nama Lengkap', data!['nama']),
                  const Divider(height: 30),
                  _buildItem('Nomor Induk Pegawai (NIP)', data!['nip']),
                  const Divider(height: 30),
                  _buildItem('Nomor Handphone', data!['no_hp'] ?? '-'),
                  const Divider(height: 30),
                  _buildItem('Status Driver', data!['status'] == 'aktif' ? 'Aktif' : 'Non-Aktif'),
                ],
              )
          )
    );
  }
  Widget _buildItem(String label, String value) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(color: Colors.grey, fontSize: 13)),
        const SizedBox(height: 6),
        Text(value, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w500, color: Colors.black87)),
      ],
    );
  }
}

class UbahPasswordPage extends StatefulWidget {
  final String token;
  const UbahPasswordPage({super.key, required this.token});
  @override State<UbahPasswordPage> createState() => _UbahPasswordPageState();
}

class _UbahPasswordPageState extends State<UbahPasswordPage> {
  final passLama = TextEditingController();
  final passBaru = TextEditingController();
  final konfBaru = TextEditingController();
  bool loading = false;
  bool obLama = true, obBaru = true, obKonf = true;

  Future<void> simpan() async {
    if (passLama.text.isEmpty || passBaru.text.isEmpty || konfBaru.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Semua field harus diisi')));
      return;
    }
    if (passBaru.text != konfBaru.text) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Konfirmasi password tidak sesuai')));
      return;
    }
    setState(() => loading = true);
    try {
      final r = await Api.ubahPassword(widget.token, passLama.text, passBaru.text);
      if (r['success'] != true) throw Exception(r['message']);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Password berhasil diubah')));
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
    } finally {
      if (mounted) setState(() => loading = false);
    }
  }

  @override Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Ubah Password', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)), backgroundColor: primaryRed, foregroundColor: Colors.white, centerTitle: true),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Password Lama', style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            TextField(
              controller: passLama,
              obscureText: obLama,
              decoration: InputDecoration(
                prefixIcon: const Icon(Icons.lock_outline, size: 20),
                suffixIcon: IconButton(icon: Icon(obLama ? Icons.visibility_off : Icons.visibility, color: Colors.grey), onPressed: () => setState(() => obLama = !obLama)),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                filled: true,
                fillColor: Colors.white
              )
            ),
            const SizedBox(height: 20),
            const Text('Password Baru', style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            TextField(
              controller: passBaru,
              obscureText: obBaru,
              decoration: InputDecoration(
                prefixIcon: const Icon(Icons.lock_outline, size: 20),
                suffixIcon: IconButton(icon: Icon(obBaru ? Icons.visibility_off : Icons.visibility, color: Colors.grey), onPressed: () => setState(() => obBaru = !obBaru)),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                filled: true,
                fillColor: Colors.white
              )
            ),
            const SizedBox(height: 20),
            const Text('Konfirmasi Password Baru', style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            TextField(
              controller: konfBaru,
              obscureText: obKonf,
              decoration: InputDecoration(
                prefixIcon: const Icon(Icons.lock_outline, size: 20),
                suffixIcon: IconButton(icon: Icon(obKonf ? Icons.visibility_off : Icons.visibility, color: Colors.grey), onPressed: () => setState(() => obKonf = !obKonf)),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                filled: true,
                fillColor: Colors.white
              )
            ),
            const SizedBox(height: 40),
            SizedBox(
              width: double.infinity,
              height: 55,
              child: FilledButton(
                style: FilledButton.styleFrom(backgroundColor: primaryRed, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
                onPressed: loading ? null : simpan,
                child: loading ? const CircularProgressIndicator(color: Colors.white) : const Text('Simpan Password', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold))
              ),
            )
          ]
        )
      )
    );
  }
}

class PusatBantuanPage extends StatelessWidget {
  const PusatBantuanPage({super.key});

  @override Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pusat Bantuan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)), backgroundColor: primaryRed, foregroundColor: Colors.white, centerTitle: true),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Hubungi Kami', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 16),
            Card(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              child: ListTile(
                leading: const CircleAvatar(backgroundColor: Color(0xffe8f5e9), child: Icon(Icons.phone, color: Colors.green)),
                title: const Text('WhatsApp Admin'),
                subtitle: const Text('+62 812-3456-7890'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () {}
              )
            ),
            const SizedBox(height: 12),
            Card(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              child: ListTile(
                leading: const CircleAvatar(backgroundColor: Color(0xffe3f2fd), child: Icon(Icons.email, color: Colors.blue)),
                title: const Text('Email Support'),
                subtitle: const Text('support@bankjatim.co.id'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () {}
              )
            ),
            const SizedBox(height: 30),
            const Text('Pertanyaan Umum (FAQ)', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 16),
            _buildFaq('Bagaimana cara input KM awal?', 'Buka menu Beranda, pilih penugasan yang aktif, lalu masukkan angka KM awal dan simpan.'),
            _buildFaq('Apa yang dilakukan jika aplikasi error?', 'Pastikan koneksi internet stabil atau coba tutup aplikasi dan buka kembali. Jika masih bermasalah, silakan hubungi admin.'),
            _buildFaq('Lupa password?', 'Silakan hubungi admin melalui WhatsApp untuk melakukan reset password akun Anda.'),
          ],
        )
      )
    );
  }

  Widget _buildFaq(String q, String a) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 20, offset: const Offset(0, 8)),
        ],
        border: Border.all(color: Colors.grey.shade100)
      ),
      child: ExpansionTile(
        shape: const Border(),
        collapsedShape: const Border(),
        tilePadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 4),
        title: Text(q, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 15)),
        iconColor: primaryRed,
        textColor: primaryRed,
        collapsedTextColor: Colors.black87,
        collapsedIconColor: Colors.grey.shade600,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 0, 20, 20),
            child: Text(a, style: const TextStyle(color: Colors.black54, fontSize: 14, height: 1.6)),
          )
        ],
      ),
    );
  }
}

class AnimatedIndexedStack extends StatefulWidget {
  final int index;
  final List<Widget> children;

  const AnimatedIndexedStack({
    super.key,
    required this.index,
    required this.children,
  });

  @override
  State<AnimatedIndexedStack> createState() => _AnimatedIndexedStackState();
}

class _AnimatedIndexedStackState extends State<AnimatedIndexedStack> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _animation;
  int _index = 0;

  @override
  void initState() {
    _controller = AnimationController(vsync: this, duration: const Duration(milliseconds: 250));
    _animation = Tween<double>(begin: 0.0, end: 1.0).animate(CurvedAnimation(parent: _controller, curve: Curves.easeInOut));
    _index = widget.index;
    _controller.forward();
    super.initState();
  }

  @override
  void didUpdateWidget(AnimatedIndexedStack oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.index != _index) {
      _controller.reverse().then((_) {
        if (mounted) {
          setState(() => _index = widget.index);
          _controller.forward();
        }
      });
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return FadeTransition(
      opacity: _animation,
      child: IndexedStack(
        index: _index,
        children: widget.children,
      ),
    );
  }
}
