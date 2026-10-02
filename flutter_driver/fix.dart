
import 'dart:io';

void main() {
  final file = File('lib/main.dart');
  String content = file.readAsStringSync();
  
  final target = '''
                    ),
                  )
                ],'''.replaceAll('\n', '\r\n');
                
  final replace = '''
                    ),
                  ),
                  const SizedBox(width: 16),
                  Image.asset('assets/img/logo.png', width: 200, color: Colors.white, fit: BoxFit.contain),
                ],'''.replaceAll('\n', '\r\n');

  if (content.contains(target)) {
    content = content.replaceFirst(target, replace);
    file.writeAsStringSync(content);
    print("Success");
  } else {
    print("Target not found!");
  }
}
