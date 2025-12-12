$(document).ready(function () {
    var questionCount = 0;

    // إضافة سؤال جديد
    $('#addQuestion').click(function () {
        questionCount++;
        addQuestion(questionCount);
    });

    // إضافة خيار جديد
    $(document).on('click', '.addOption', function () {
        var questionCount = $(this).data('question');
        addOption(questionCount);
    });

    // حذف خيار
    $(document).on('click', '.removeOption', function () {
        if (confirm('هل أنت متأكد من أنك تريد حذف هذا الخيار؟')) {
            $(this).parent().remove();
        }
    });

    // حذف سؤال
    $(document).on('click', '.removeQuestion', function () {
        if (confirm('هل أنت متأكد من أنك تريد حذف هذا السؤال؟')) {
            $(this).parent().remove();
        }
    });

    // تغيير نوع السؤال
    $(document).on('change', '.questionType', function () {
        var questionCount = $(this).attr('id').replace('questionType', '');
        toggleOptions(questionCount);
    });

    // إرسال النموذج باستخدام AJAX
    $('#testForm').submit(function (event) {
        event.preventDefault();
        submitForm();
    });

    // إعداد Sortable
    var questionsContainer = document.getElementById('questionsContainer');
    if (questionsContainer && typeof Sortable !== 'undefined') {
        Sortable.create(questionsContainer, {
            handle: '.drag-handle',
            animation: 150
        });
    }
});

function addQuestion(questionCount) {
    // Generate a reasonably unique ID part to avoid collisions if many are added/removed
    var uniqueId = Date.now() + '_' + Math.floor(Math.random() * 1000);
    // However, the backend might rely on sequential indices or just arrays.
    // The original code used 'questionCount' as a simple counter.
    // To keep it simple and consistent with form array logic (name="questionTitle[]"), unique IDs for DOM elements are fine.

    // We will stick to the counter passed in, but ensure it's used for ID attributes only.
    // The name attributes use [] so PHP handles them as arrays.

    var questionHTML = `
        <div class="question bg-white mb-3 p-4 rounded-2 shadow-sm" id="question${questionCount}">
            <div class="questionContent">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-floating me-3 w-50">
                            <input type="text" id="questionTitle${questionCount}" name="questionTitle[]" class="form-control" required>
                            <label for="questionTitle${questionCount}">عنوان السؤال</label>
                        </div>
                        <div class="form-floating me-3">
                            <select class="questionType form-select" id="questionType${questionCount}" name="questionType[]" required>
                                <option selected>--</option>
                                <option value="text">نص</option>
                                <option value="choice">إختيار من متعدد</option>
                            </select>
                            <label for="questionType${questionCount}">نوع السؤال</label>
                        </div>
                        <div class="form-floating w-25">
                            <input type="number" id="questionScore${questionCount}" name="questionScore[]" class="form-control" required>
                            <label for="questionScore${questionCount}">الدرجة</label>
                        </div>
                    </div>
                    <div class="correctTextAnswerContainer form-floating mb-3" id="correctTextAnswerContainer${questionCount}" style="display: none;">
                        <input type="text" class="correctTextAnswer form-control" name="correctTextAnswer[]">
                        <label>الإجابة الصحيحة</label>
                    </div>
                    <div class="optionsContainer" id="optionsContainer${questionCount}" style="display: none;">
                        <button type="button" class="addOption" data-question="${questionCount}">إضافة خيار</button>
                        <div class="optionsList d-flex flex-column gap-2 mt-2"></div>
                    </div>
                </div>
                <i class="fa-solid fa-ellipsis-vertical drag-handle d-flex justify-content-center align-items-center" aria-hidden="true" style="height: 4.6rem;width: 2rem; cursor: move;"></i>
            </div>
            <button type="button" class="removeQuestion btn btn-danger btn-sm mt-2"><i class="fa-solid fa-trash-can"></i> حذف السؤال</button>
        </div>`;
    $('#questionsContainer').append(questionHTML);
}

function addOption(questionCount) {
    // We need to group options by question in the backend.
    // The original code used name="options[${questionCount}][]".
    // This relies on questionCount being consistent.

    var optionsList = $('#optionsContainer' + questionCount + ' .optionsList');
    var optionIndex = optionsList.children().length + 1; // 1-based index for visual or value

    optionsList.append(`
        <div class="d-flex justify-content-between align-items-center">
            <input type="text" class="option form-control me-2" name="options[${questionCount}][]" placeholder="الخيار" required>
            <div class="form-check">
                <input type="checkbox" class="correctCheckbox form-check-input" name="correctAnswer[${questionCount}][]" value="${optionIndex}">
            </div>
            <button type="button" class="removeOption btn btn-outline-danger btn-sm ms-2">حذف</button>
        </div>`);
}

function toggleOptions(questionCount) {
    var questionType = $('#questionType' + questionCount).val();
    if (questionType == 'text') {
        $('#optionsContainer' + questionCount).hide();
        $('#correctTextAnswerContainer' + questionCount).show();
    } else if (questionType == 'choice') {
        $('#optionsContainer' + questionCount).show();
        $('#correctTextAnswerContainer' + questionCount).hide();
    } else {
        $('#optionsContainer' + questionCount).hide();
        $('#correctTextAnswerContainer' + questionCount).hide();
    }
}

function submitForm() {
    var formData = $('#testForm').serialize();
    var actionUrl = $('#testForm').attr('action');

    $.ajax({
        type: 'POST',
        url: actionUrl,
        data: formData,
        success: function (response) {
            // Check if response is JSON or text
            if (typeof response === 'string') {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    // response is text
                }
            }

            if (response.success || response.status === 'success') {
                 // Use Bootstrap Modal if available, else fallback to alert
                if (typeof bootstrap !== 'undefined' && document.getElementById('commonModal')) {
                     var messageDiv = document.getElementById('messageDiv');
                     if(messageDiv) messageDiv.innerHTML = `<div class="text-center p-3"><i class="fa-regular fa-circle-check text-success fa-3x"></i><h6 class="mt-3">تم إرسال الاختبار بنجاح</h6></div>`;
                     var commonModal = new bootstrap.Modal(document.getElementById('commonModal'));
                     commonModal.show();
                     setTimeout(() => {
                         commonModal.hide();
                         // Optional: redirect or reset form
                     }, 2000);
                } else {
                    alert('تم إرسال الاختبار بنجاح');
                }
            } else {
                 alert('حدث خطأ: ' + (response.message || 'استجابة غير معروفة'));
            }
            console.log(response);
        },
        error: function (xhr, status, error) {
            alert('حدث خطأ أثناء إرسال الاختبار');
            console.error(xhr.responseText);
        }
    });
}