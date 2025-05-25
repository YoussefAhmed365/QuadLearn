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
    Sortable.create(document.getElementById('questionsContainer'), {
        handle: '.drag-handle',
        animation: 150
    });
});

function addQuestion(questionCount) {
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
                        <div class="optionsList d-flex flex-column gap-2"></div>
                    </div>
                </div>
                <i class="fa-solid fa-ellipsis-vertical drag-handle d-flex justify-content-center align-items-center" aria-hidden="true" style="height: 4.6rem;width: 2rem;"></i>
            </div>
            <button type="button" class="removeQuestion"><i class="fa-solid fa-trash-can"></i></button>
        </div>`;
    $('#questionsContainer').append(questionHTML);
}

function addOption(questionCount) {
    $('#optionsContainer' + questionCount + ' .optionsList').append(`
        <div class="d-flex justify-content-between align-items-center">
            <input type="text" class="option" name="options[${questionCount}][]" required>
            <input type="checkbox" class="correctCheckbox" name="correctAnswer[${questionCount}][]" value="${$('#optionsContainer' + questionCount + ' .optionsList input').length + 1}">
            <button type="button" class="removeOption">حذف</button>
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

    $.ajax({
        type: 'POST',
        url: $('#testForm').attr('action'),
        data: formData,
        success: function (response) {
            alert('تم إرسال الاختبار بنجاح');
            console.log(response);
        },
        error: function (xhr, status, error) {
            alert('حدث خطأ أثناء إرسال الاختبار');
            console.error(xhr.responseText);
        }
    });
}