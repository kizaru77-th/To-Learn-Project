<?php
$chapter = $_GET['chapter'] ?? 'chapter-1';
$returnTo = $_GET['returnTo'] ?? 'M4math.html';

$chapterNames = [
    'chapter-1' => 'เซต',
    'chapter-2' => 'ตรรกศาสตร์',
    'chapter-3' => 'จำนวนจริง',
    'chapter-4' => 'ความสัมพันธ์และฟังก์ชัน',
    'chapter-5' => 'ฟังก์ชันเอกซ์โพเนนเชียลและลอการิทึม',
    'chapter-6' => 'เรขาคณิตวิเคราะห์และภาคตัดกรวย',
];

$questions = [
    'chapter-1' => [
        ['text' => 'เซต A = {1, 2, 3} ข้อใดต่อไปนี้เป็นสมาชิกของเซต A', 'options' => ['4', '2', '5', '6'], 'answer' => '2'],
        ['text' => 'ถ้า A = {1, 2, 3} และ B = {3, 2, 1} แล้วข้อความใดถูกต้อง', 'options' => ['A ≠ B', 'A ⊂ B', 'A = B', 'A ∩ B = ∅'], 'answer' => 'A = B'],
        ['text' => 'ถ้า A = {1, 2} และ B = {1, 2, 3} ข้อใดถูกต้อง', 'options' => ['A ⊂ B', 'B ⊂ A', 'A = B', 'A ∩ B = ∅'], 'answer' => 'A ⊂ B'],
        ['text' => 'สัญลักษณ์ ∅ หมายถึงอะไร', 'options' => ['เซตจำกัด', 'เซตว่าง', 'สับเซต', 'เอกภพสัมพัทธ์'], 'answer' => 'เซตว่าง'],
        ['text' => 'ผลของ A ∪ B เมื่อ A = {1, 2} และ B = {2, 3} คือข้อใด', 'options' => ['{1}', '{2}', '{1, 2, 3}', '{3}'], 'answer' => '{1, 2, 3}'],
        ['text' => 'ผลของ A ∩ B เมื่อ A = {1, 2} และ B = {2, 3} คือข้อใด', 'options' => ['{1}', '{2}', '{3}', '{1, 2, 3}'], 'answer' => '{2}'],
        ['text' => 'เซตของจำนวนเต็มบวกเริ่มจาก 1 ข้อใดคือคำตอบที่ถูก', 'options' => ['ℕ', 'ℤ', 'ℝ', '∅'], 'answer' => 'ℕ'],
        ['text' => 'ถ้า A = {a, b} แล้วเพาเวอร์เซตของ A มีสมาชิกกี่ตัว', 'options' => ['2', '3', '4', '5'], 'answer' => '4'],
        ['text' => 'คำว่า “สมาชิก” ในเซตหมายถึงอะไร', 'options' => ['สิ่งที่อยู่ในเซต', 'สิ่งที่อยู่นอกเซต', 'ตัวดำเนินการ', 'คำตอบของสมการ'], 'answer' => 'สิ่งที่อยู่ในเซต'],
        ['text' => 'ตัวแปร x ในคำว่า B = {x | x เป็นจำนวนเต็มบวก} หมายถึงอะไร', 'options' => ['สมาชิกของเซต B', 'จำนวนจริงเท่านั้น', 'คำตอบที่ไม่ถูก', 'พหุนาม'], 'answer' => 'สมาชิกของเซต B'],
    ],
    'chapter-2' => [
        ['text' => 'ประพจน์คือข้อใด', 'options' => ['คำถามที่ยังไม่รู้คำตอบ', 'ข้อความที่บอกค่าได้ว่าเป็นจริงหรือเท็จ', 'คำสั่งให้ทำ', 'คำอธิบายแบบไม่มีกฎ'], 'answer' => 'ข้อความที่บอกค่าได้ว่าเป็นจริงหรือเท็จ'],
        ['text' => 'ข้อความ “2 + 3 = 5” เป็นข้อใด', 'options' => ['ประพจน์จริง', 'ประพจน์เท็จ', 'ประโยคเปิด', 'คำถาม'], 'answer' => 'ประพจน์จริง'],
        ['text' => 'ถ้า p แทน “น้ำร้อน” และ q แทน “น้ำเดือด” คำว่า p ∧ q หมายถึงอะไร', 'options' => ['p หรือ q', 'p และ q', 'ไม่ p', 'p ถ้า q'], 'answer' => 'p และ q'],
        ['text' => 'คำว่า p ∨ q หมายถึงอะไร', 'options' => ['p และ q', 'p หรือ q', 'ไม่ p', 'ถ้า p แล้ว q'], 'answer' => 'p หรือ q'],
        ['text' => 'สัญลักษณ์ ¬p หมายถึงอะไร', 'options' => ['p และ q', 'ไม่ p', 'p หรือ q', 'ถ้า p แล้ว q'], 'answer' => 'ไม่ p'],
        ['text' => 'ข้อความ “ถ้าเดือนมี 30 วัน แล้วเดือนนั้นมี 4 สัปดาห์” เป็นคำถามประเภทใด', 'options' => ['ประพจน์', 'ประโยคเปิด', 'คำสั่ง', 'คำถาม'], 'answer' => 'ประพจน์'],
        ['text' => 'ข้อใดเป็นตัวเชื่อมประพจน์', 'options' => ['+', '-', '∧', '×'], 'answer' => '∧'],
        ['text' => 'ค่าความจริงของ p ∧ q จะเป็นจริงก็ต่อเมื่อ', 'options' => ['p จริงและ q จริง', 'p จริงหรือ q จริง', 'p เท็จและ q เท็จ', 'p เท็จหรือ q เท็จ'], 'answer' => 'p จริงและ q จริง'],
        ['text' => 'ค่าความจริงของ p ∨ q จะเป็นเท็จก็ต่อเมื่อ', 'options' => ['p เท็จและ q เท็จ', 'p จริงและ q จริง', 'p จริงและ q เท็จ', 'p เท็จและ q จริง'], 'answer' => 'p เท็จและ q เท็จ'],
        ['text' => 'ข้อความ “x + 2 = 5” เป็นข้อใด', 'options' => ['ประพจน์', 'ประโยคเปิด', 'คำสั่ง', 'ข้อความเท็จ'], 'answer' => 'ประโยคเปิด'],
    ],
    'chapter-3' => [
        ['text' => 'จำนวนเต็มบวกทั้งหมดเป็นเซตของอะไร', 'options' => ['ℕ', 'ℤ', 'ℝ', '∅'], 'answer' => 'ℕ'],
        ['text' => 'จำนวนจริงประกอบด้วยอะไรบ้าง', 'options' => ['จำนวนเต็มและเศษส่วน', 'เฉพาะจำนวนเต็ม', 'เฉพาะจำนวนตรรกยะ', 'เฉพาะจำนวนคี่'], 'answer' => 'จำนวนเต็มและเศษส่วน'],
        ['text' => 'ค่าของ |−7| คือข้อใด', 'options' => ['−7', '7', '0', '−1'], 'answer' => '7'],
        ['text' => 'ถ้า x + 3 = 8 ค่าของ x คือข้อใด', 'options' => ['3', '5', '8', '11'], 'answer' => '5'],
        ['text' => 'ถ้า 2x = 10 ค่าของ x คือข้อใด', 'options' => ['2', '5', '10', '20'], 'answer' => '5'],
        ['text' => 'สมการ |x| = 3 มีคำตอบกี่ค่า', 'options' => ['1', '2', '3', '0'], 'answer' => '2'],
        ['text' => 'จำนวนจริงที่อยู่ระหว่าง 1 และ 3 มีข้อใดต่อไปนี้', 'options' => ['1', '2', '3', '4'], 'answer' => '2'],
        ['text' => 'คำว่า “พหุนาม” หมายถึงอะไร', 'options' => ['นิพจน์ที่มีตัวแปรและเลขชี้กำลัง', 'จำนวนเต็มเท่านั้น', 'อสมการอย่างเดียว', 'คำตอบของสมการ'], 'answer' => 'นิพจน์ที่มีตัวแปรและเลขชี้กำลัง'],
        ['text' => 'ผลลัพธ์ของ x^2 − 1 เมื่อ x = 2 คือข้อใด', 'options' => ['1', '2', '3', '4'], 'answer' => '3'],
        ['text' => 'ถ้า x = −2 แล้ว 3x + 1 เท่ากับข้อใด', 'options' => ['−5', '−3', '5', '7'], 'answer' => '−5'],
    ],
    'chapter-4' => [
        ['text' => 'ความสัมพันธ์ระหว่างสิ่งสองสิ่งเรียกว่าอะไร', 'options' => ['ฟังก์ชัน', 'ความสัมพันธ์', 'จำนวนจริง', 'เซตว่าง'], 'answer' => 'ความสัมพันธ์'],
        ['text' => 'ถ้ากำหนด A = {1, 2} และ B = {a, b} ผลคูณคาร์ทีเซียน A × B มีจำนวนคู่อันดับเท่าใด', 'options' => ['2', '4', '6', '8'], 'answer' => '4'],
        ['text' => 'ฟังก์ชันคือความสัมพันธ์ที่มีลักษณะอย่างไร', 'options' => ['ทุกสมาชิกในโดเมนมีคู่ค่าน้อยกว่า 1', 'ทุกสมาชิกในโดเมนมีค่าใกล้เคียงกัน', 'ทุกสมาชิกในโดเมนมีค่าออกมาเพียงค่าเดียว', 'ไม่มีเงื่อนไข'], 'answer' => 'ทุกสมาชิกในโดเมนมีค่าออกมาเพียงค่าเดียว'],
        ['text' => 'คำว่าโดเมนหมายถึงอะไร', 'options' => ['ชุดของค่า x ที่ใช้ได้', 'ชุดของค่า y ที่ได้', 'จำนวนเต็ม', 'เซตว่าง'], 'answer' => 'ชุดของค่า x ที่ใช้ได้'],
        ['text' => 'คำว่าเรนจ์หมายถึงอะไร', 'options' => ['ชุดของค่า x ที่ใช้ได้', 'ชุดของค่า y ที่เกิดขึ้น', 'เซตของจำนวนเต็ม', 'คำตอบของสมการ'], 'answer' => 'ชุดของค่า y ที่เกิดขึ้น'],
        ['text' => 'กราฟของความสัมพันธ์มักแสดงด้วยสิ่งใด', 'options' => ['จุดบนระนาบ', 'ตัวเลขเดียว', 'คำแปล', 'สัญลักษณ์เฉพาะ'], 'answer' => 'จุดบนระนาบ'],
        ['text' => 'ข้อใดคือฟังก์ชันเชิงเส้น', 'options' => ['y = x + 1', 'y = x^2', 'y = 1/x', 'y = √x'], 'answer' => 'y = x + 1'],
        ['text' => 'ในฟังก์ชัน y = 2x + 3 ค่าสัมประสิทธิ์ของ x คือข้อใด', 'options' => ['2', '3', '5', 'x'], 'answer' => '2'],
        ['text' => 'ถ้า x = 1 ในฟังก์ชัน y = x + 2 ค่า y เท่ากับข้อใด', 'options' => ['1', '2', '3', '4'], 'answer' => '3'],
        ['text' => 'ฟังก์ชันยิ่งใหญ่กว่า 1 ตัวแปรนิยมเขียนในรูปแบบใด', 'options' => ['y = f(x)', 'x = y', 'A = B', '2 + 3'], 'answer' => 'y = f(x)'],
    ],
    'chapter-5' => [
        ['text' => 'เลขยกกำลังแบบ a^2 หมายถึงอะไร', 'options' => ['a + a', 'a × a', 'a ÷ a', 'a − a'], 'answer' => 'a × a'],
        ['text' => 'a^m × a^n เท่ากับข้อใด', 'options' => ['a^(m+n)', 'a^(m−n)', 'a^(m×n)', 'a^(m/n)'], 'answer' => 'a^(m+n)'],
        ['text' => 'a^m ÷ a^n เท่ากับข้อใด', 'options' => ['a^(m+n)', 'a^(m−n)', 'a^(m×n)', 'a^(m/n)'], 'answer' => 'a^(m−n)'],
        ['text' => 'ฟังก์ชันเอกซ์โพเนนเชียลมักมีรูปร่างอย่างไร', 'options' => ['เส้นตรง', 'โค้งชัน', 'วงกลม', 'พาราโบลา'], 'answer' => 'โค้งชัน'],
        ['text' => 'ฟังก์ชันลอการิทึมเป็นฟังก์ชันผกผันของอะไร', 'options' => ['ฟังก์ชันเชิงเส้น', 'ฟังก์ชันเอกซ์โพเนนเชียล', 'ฟังก์ชันตรีโกณมิติ', 'ฟังก์ชันกำลังสอง'], 'answer' => 'ฟังก์ชันเอกซ์โพเนนเชียล'],
        ['text' => 'คำว่า log_a b หมายถึงอะไร', 'options' => ['เลขชี้กำลังที่ต้องยก a เพื่อให้ได้ b', 'ผลบวกของ a กับ b', 'ผลต่างของ a กับ b', 'คำตอบของสมการ'], 'answer' => 'เลขชี้กำลังที่ต้องยก a เพื่อให้ได้ b'],
        ['text' => 'ถ้า 2^3 = 8 แล้ว log_2 8 เท่ากับข้อใด', 'options' => ['2', '3', '4', '8'], 'answer' => '3'],
        ['text' => 'สมบัติของ log_a (xy) เท่ากับข้อใด', 'options' => ['log_a x + log_a y', 'log_a x − log_a y', 'log_a x × log_a y', 'log_a x / log_a y'], 'answer' => 'log_a x + log_a y'],
        ['text' => 'ถ้า log_10 100 = 2 แล้ว 10^2 เท่ากับข้อใด', 'options' => ['10', '100', '1000', '10000'], 'answer' => '100'],
        ['text' => 'ค่า x ในสมการ 2^x = 8 คือข้อใด', 'options' => ['2', '3', '4', '8'], 'answer' => '3'],
    ],
    'chapter-6' => [
        ['text' => 'ระยะห่างระหว่างจุดสองจุดบนระนาบมักใช้สูตรใด', 'options' => ['สูตรโค้ง', 'สูตรระยะห่าง', 'สูตรพื้นที่', 'สูตรราก'], 'answer' => 'สูตรระยะห่าง'],
        ['text' => 'เส้นตรงที่มีความชัน m และตัดแกน y ที่ b เขียนเป็นสมการใด', 'options' => ['y = mx + b', 'y = x^2 + b', 'y = m + b', 'y = b'], 'answer' => 'y = mx + b'],
        ['text' => 'เส้นตรงขนานกันจะมีความชันอย่างไร', 'options' => ['ต่างกัน', 'เท่ากัน', 'เป็นลบ', 'เป็นศูนย์'], 'answer' => 'เท่ากัน'],
        ['text' => 'เส้นตรงตั้งฉากจะมีความสัมพันธ์อย่างไร', 'options' => ['ผลคูณของความชันเท่ากับ −1', 'ผลคูณของความชันเท่ากับ 1', 'ความชันเท่ากัน', 'ความชันเป็นศูนย์'], 'answer' => 'ผลคูณของความชันเท่ากับ −1'],
        ['text' => 'ภูมิภาคที่ได้จากการตัดของกรวยและระนาบเรียกว่าอะไร', 'options' => ['ภาคตัดกรวย', 'พหุนาม', 'ความสัมพันธ์', 'ฟังก์ชัน'], 'answer' => 'ภาคตัดกรวย'],
        ['text' => 'วงกลมมีสมบัติสำคัญอย่างไร', 'options' => ['ทุกจุดห่างจากจุดศูนย์กลางเท่ากัน', 'มีเพียง 2 จุด', 'เส้นตรงยาวเท่ากัน', 'ไม่มีรัศมี'], 'answer' => 'ทุกจุดห่างจากจุดศูนย์กลางเท่ากัน'],
        ['text' => 'วงรีมีรูปร่างอย่างไร', 'options' => ['ทรงกลม', 'รูปไข่', 'รูปสามเหลี่ยม', 'เส้นตรง'], 'answer' => 'รูปไข่'],
        ['text' => 'พาราโบลาเป็นกราฟของฟังก์ชันประเภทใด', 'options' => ['เชิงเส้น', 'กำลังสอง', 'ลอการิทึม', 'ตรีโกณมิติ'], 'answer' => 'กำลังสอง'],
        ['text' => 'ไฮเพอร์โบลาเป็นเส้นโค้งประเภทใด', 'options' => ['เปิดออก', 'ปิดวง', 'ตรง', 'ทแยง'], 'answer' => 'เปิดออก'],
        ['text' => 'ระยะห่างจากจุดไปยังเส้นตรงเป็นคำจำกัดความของอะไร', 'options' => ['ความสูง', 'ระยะห่าง', 'พื้นที่', 'พิกัด'], 'answer' => 'ระยะห่าง'],
    ],
];

$currentQuestions = $questions[$chapter] ?? $questions['chapter-1'];
$chapterLabel = $chapterNames[$chapter] ?? 'บทเรียน';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบทดสอบท้ายบท | ToLearn</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { color-scheme: light; }
        body {
            margin: 0;
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #dff4ff 0%, #fef3c7 100%);
            color: #1f2937;
            min-height: 100vh;
            padding: 24px;
        }
        .quiz-card {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.15);
        }
        .quiz-header { margin-bottom: 20px; }
        .quiz-header h1 { margin: 0 0 8px; font-size: 28px; }
        .quiz-header p { margin: 0; color: #475569; }
        .question-block { margin-bottom: 18px; padding: 16px; border-radius: 16px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .question-block p { margin: 0 0 12px; font-weight: 600; }
        .question-block label { display: block; padding: 8px 10px; border-radius: 10px; margin-bottom: 8px; background: white; cursor: pointer; }
        .question-block input { margin-right: 8px; }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 20px; }
        button, .back-link {
            border: none;
            padding: 12px 18px;
            border-radius: 999px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }
        button { background: #2563eb; color: white; }
        .back-link { background: #e2e8f0; color: #0f172a; }
        .result-box { margin-top: 18px; padding: 16px; border-radius: 16px; background: #fef3c7; display: none; }
        .result-box.success { background: #dcfce7; }
        .result-box.fail { background: #fee2e2; }
        @media (max-width: 768px) {
            body { padding: 12px; }
            .quiz-card { padding: 18px; }
        }
    </style>
</head>
<body>
    <div class="quiz-card">
        <div class="quiz-header">
            <h1>แบบทดสอบท้ายบท: <?= htmlspecialchars($chapterLabel) ?></h1>
            <p>ทำให้ได้ 7/10 ขึ้นไป เพื่อปลดล็อกบทเรียนถัดไป</p>
        </div>

        <form id="quizForm">
            <?php foreach ($currentQuestions as $index => $item): ?>
                <div class="question-block">
                    <p><?= $index + 1 ?>. <?= htmlspecialchars($item['text']) ?></p>
                    <?php foreach ($item['options'] as $option): ?>
                        <label>
                            <input type="radio" name="q<?= $index + 1 ?>" value="<?= htmlspecialchars($option) ?>" required>
                            <?= htmlspecialchars($option) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <div class="actions">
                <button type="submit">ตรวจคำตอบ</button>
                <a class="back-link" href="<?= htmlspecialchars($returnTo) ?>">กลับไปหน้าบทเรียน</a>
            </div>
        </form>

        <div class="result-box" id="resultBox"></div>
    </div>

    <script>
        const quizForm = document.getElementById('quizForm');
        const resultBox = document.getElementById('resultBox');
        const chapter = new URLSearchParams(window.location.search).get('chapter') || 'chapter-1';
        const returnTo = new URLSearchParams(window.location.search).get('returnTo') || 'M4math.html';
        const correctAnswers = <?= json_encode(array_map(fn($item) => $item['answer'], $currentQuestions)) ?>;
        const cooldownKey = `quizCooldown_${chapter}`;
        const cooldownDuration = 5 * 60 * 1000;

        if (Number(localStorage.getItem(cooldownKey) || 0) > Date.now()) {
            window.location.replace(returnTo);
        }

        quizForm.addEventListener('submit', function (event) {
            event.preventDefault();

            let score = 0;
            correctAnswers.forEach((answer, index) => {
                const selected = document.querySelector(`input[name="q${index + 1}"]:checked`);
                if (selected && selected.value === answer) {
                    score += 1;
                }
            });

            const passed = score >= 7;
            const message = `คุณทำได้ ${score}/10 คะแนน<br>${passed ? 'ยินดีด้วย! คุณผ่านเกณฑ์ 7/10 และปลดล็อกบทเรียนถัดไปแล้ว' : 'ยังไม่ผ่านเกณฑ์ 7/10 กรุณาทำใหม่อีกครั้ง'}`;
            resultBox.innerHTML = message;
            resultBox.className = `result-box ${passed ? 'success' : 'fail'}`;
            resultBox.style.display = 'block';

            if (passed) {
                const stored = JSON.parse(localStorage.getItem('m4UnlockedChapters') || '[]');
                if (!stored.includes(chapter)) {
                    stored.push(chapter);
                }
                localStorage.setItem('m4UnlockedChapters', JSON.stringify(stored));
                localStorage.setItem('m4LastActiveChapter', chapter);
                localStorage.removeItem(cooldownKey);
            } else {
                localStorage.setItem(cooldownKey, String(Date.now() + cooldownDuration));
                window.location.href = returnTo;
                return;
            }

            const backLink = document.querySelector('.back-link');
            if (backLink) {
                backLink.href = returnTo;
            }

        });
    </script>
</body>
</html>
