/**
 * progress-tracker.js
 * ระบบติดตามและบันทึกความคืบหน้าการเรียนลงใน Database รายบัญชีผู้ใช้
 */

(function () {
    // 1. ระบุรหัสวิชา (Course ID) ตามชื่อไฟล์ปัจจุบัน
    function getCourseId() {
        const path = window.location.pathname.split('/').pop().toLowerCase();
        const customCourseId = document.body ? document.body.getAttribute('data-course-id') : null;
        if (customCourseId) return customCourseId;

        if (path.includes('m4math')) return 'math_m4';
        if (path.includes('m5math')) return 'math_m5';
        if (path.includes('m6math')) return 'math_m6';
        if (path.includes('mathlearn')) return 'math';
        if (path.includes('thaimainlearn')) return 'thai_main';
        if (path.includes('thailearn')) return 'thai';
        if (path.includes('sciencelearnbio')) return 'science_bio';
        if (path.includes('sciencelearnche')) return 'science_chem';
        if (path.includes('sciencelearnpysic')) return 'science_phys';
        if (path.includes('sciencelearn')) return 'science';
        if (path.includes('englishlearn')) return 'english';
        if (path.includes('sociallearn')) return 'social';
        
        return path.replace('.html', '') || 'general';
    }

    const currentCourseId = getCourseId();
    let saveTimeout = null;
    let lastSavedLessonId = '';

    // 2. ฟังก์ชันส่งข้อมูลบันทึกลง Database
    function saveProgressToDB(lessonId, lessonTitle) {
        if (!lessonId || lessonId === lastSavedLessonId) return;

        fetch('save-progress.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                course_id: currentCourseId,
                last_lesson_id: lessonId,
                last_lesson_title: lessonTitle || lessonId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                lastSavedLessonId = lessonId;
                showSaveToast(lessonTitle || 'บันทึกบทเรียนแล้ว');
            }
        })
        .catch(err => console.error('Save progress error:', err));
    }

    // 3. ฟังก์ชันดึงความคืบหน้าจาก Database เมื่อเปิดหน้าเว็บ
    function loadProgressFromDB() {
        fetch(`get-progress.php?course_id=${encodeURIComponent(currentCourseId)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.progress && data.progress.last_lesson_id) {
                    const savedId = data.progress.last_lesson_id;
                    const savedTitle = data.progress.last_lesson_title;
                    restoreLessonPosition(savedId, savedTitle);
                }
            })
            .catch(err => console.error('Load progress error:', err));
    }

    // 4. ฟังก์ชันพาย้อนกลับไปยังบทเรียนล่าสุด
    function restoreLessonPosition(lessonId, lessonTitle) {
        let targetId = lessonId.startsWith('#') ? lessonId : '#' + lessonId;
        let btn = document.querySelector(`.sub-menu-btn[data-target="${targetId}"]`) ||
                  document.querySelector(`.sub-menu-btn[data-target="${targetId.substring(1)}"]`);
        let targetEl = document.querySelector(targetId);

        if (btn) {
            // เปิด Dropdown แม่ของปุ่มบทเรียน (ถ้ามี)
            const parentDropdown = btn.closest('.dropdown-content');
            if (parentDropdown && parentDropdown.style.display !== 'block') {
                parentDropdown.style.display = 'block';
                const parentBtn = parentDropdown.previousElementSibling;
                if (parentBtn && parentBtn.classList.contains('dropdown-btn')) {
                    parentBtn.classList.add('active');
                }
            }
            btn.click();
        } else if (targetEl) {
            targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        showResumeNotice(lessonTitle);
    }

    // 5. แสดงกล่องแจ้งเตือนขนาดเล็กเมื่อเรียนต่อจากเดิม
    function showResumeNotice(lessonTitle) {
        const notice = document.createElement('div');
        notice.id = 'progress-resume-notice';
        notice.innerHTML = `
            <div style="position: fixed; bottom: 20px; right: 20px; background: rgba(0, 30, 60, 0.95); color: #fff; padding: 12px 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); border: 1px solid #00aaff; font-family: 'Prompt', sans-serif; font-size: 14px; z-index: 99999; display: flex; align-items: center; gap: 10px; animation: fadeIn 0.5s ease;">
                <span>📖 เรียนต่อจากเดิม: <strong>${escapeHTML(lessonTitle)}</strong></span>
                <button onclick="this.parentElement.remove()" style="background:none; border:none; color:#aaa; font-size:16px; cursor:pointer; margin-left:5px;">✕</button>
            </div>
        `;
        document.body.appendChild(notice);

        setTimeout(() => {
            if (notice && notice.parentElement) {
                notice.remove();
            }
        }, 5000);
    }

    // Toast แสดงเมื่อเซฟเรียบร้อย
    function showSaveToast(lessonTitle) {
        let existing = document.getElementById('progress-save-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'progress-save-toast';
        toast.innerHTML = `
            <div style="position: fixed; bottom: 20px; left: 20px; background: rgba(40, 167, 69, 0.9); color: #fff; padding: 8px 16px; border-radius: 20px; font-family: 'Prompt', sans-serif; font-size: 12px; z-index: 99999; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                ✓ บันทึกบทเรียน: ${escapeHTML(lessonTitle)}
            </div>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast && toast.parentElement) toast.remove();
        }, 3000);
    }

    function escapeHTML(str) {
        if (!str) return '';
        return str.replace(/[&<>'"]/g, 
            tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
        );
    }

    // 6. ผูก Event Listener ดักจับการคลิกบทเรียน
    document.addEventListener('DOMContentLoaded', () => {
        // ดึงความคืบหน้าเดิมเมื่อเปิดหน้าเว็บ
        loadProgressFromDB();

        // ฟัง Event คลิกที่ปุ่ม sub-menu-btn
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.sub-menu-btn');
            if (btn) {
                const target = btn.getAttribute('data-target');
                const title = btn.textContent.trim();
                if (target) {
                    saveProgressToDB(target, title);
                }
            }
        });

        // ดักฟังสกอร์ลของ board-body หรือ window เพื่อบันทึกบทเรียนปัจจุบันที่ปรากฏอยู่บนหน้าจอ
        const boardBody = document.querySelector('.board-body') || window;
        const scrollContainer = document.querySelector('.board-body') ? document.querySelector('.board-body') : document.documentElement;

        scrollContainer.addEventListener('scroll', () => {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                const sections = document.querySelectorAll('section[id], div[id].mathstudyinside, .content-section[id]');
                let visibleSection = null;

                sections.forEach(sec => {
                    const rect = sec.getBoundingClientRect();
                    if (rect.top >= 0 && rect.top <= window.innerHeight * 0.5) {
                        visibleSection = sec;
                    }
                });

                if (visibleSection && visibleSection.id) {
                    const sectionId = '#' + visibleSection.id;
                    const matchingBtn = document.querySelector(`.sub-menu-btn[data-target="${sectionId}"]`);
                    const title = matchingBtn ? matchingBtn.textContent.trim() : (visibleSection.querySelector('h1, h2, h3')?.textContent.trim() || visibleSection.id);
                    saveProgressToDB(sectionId, title);
                }
            }, 1000);
        });
    });
})();
