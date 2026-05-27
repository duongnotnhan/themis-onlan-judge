document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('input').forEach(input => {
		input.addEventListener('input', function () {
			validateInput(this);
		});
	});
});

function validateInput(input) {
	const name = input.name;
	const value = input.value.trim();

	if (isMarkField(name)) {
		validateMark(input, value);
	} else if (isTimeLimitField(name)) {
		validateTimeLimit(input, value);
	} else if (isMemoryLimitField(name)) {
		validateMemoryLimit(input, value);
	} else {
		input.setCustomValidity('');
	}

	input.reportValidity();
}

function isMarkField(name) {
	return name === 'Mark' || name.includes('[Mark]');
}

function isTimeLimitField(name) {
	return name === 'TimeLimit' || name.includes('[TimeLimit]');
}

function isMemoryLimitField(name) {
	return name === 'MemoryLimit' || name.includes('[MemoryLimit]');
}

function validateMark(input, value) {
	const allowShared = input.name.includes('[Mark]');

	if (allowShared) {
		if (!validateFloat(value, true)) {
			input.setCustomValidity('Điểm phải là số thực lớn hơn 0 (hoặc bằng -1 nếu dùng cài đặt chung).');
			return;
		}
	} else if (!validateFloat(value, false)) {
		input.setCustomValidity('Điểm chung mỗi test phải là số thực lớn hơn 0.');
		return;
	}

	input.setCustomValidity('');
}

function validateTimeLimit(input, value) {
	const allowShared = input.name.includes('[TimeLimit]');

	if (allowShared) {
		if (!validateFloat(value, true)) {
			input.setCustomValidity('Giới hạn thời gian phải là số thực lớn hơn 0 (hoặc bằng -1 nếu dùng cài đặt chung).');
			return;
		}
	} else if (!validateFloat(value, false)) {
		input.setCustomValidity('Giới hạn thời gian chung mỗi test phải là số thực lớn hơn 0.');
		return;
	}

	input.setCustomValidity('');
}

function validateMemoryLimit(input, value) {
	const allowShared = input.name.includes('[MemoryLimit]');

	if (allowShared) {
		if (!validateInt(value, true)) {
			input.setCustomValidity('Giới hạn bộ nhớ phải là số nguyên lớn hơn hoặc bằng 1 (hoặc bằng -1 nếu dùng cài đặt chung).');
			return;
		}
	} else if (!validateInt(value, false)) {
		input.setCustomValidity('Giới hạn bộ nhớ chung mỗi test phải là số nguyên lớn hơn hoặc bằng 1.');
		return;
	}

	input.setCustomValidity('');
}

function confirmSave(event) {
	let isValid = true;

	document.querySelectorAll('input').forEach(input => {
		validateInput(input);
		if (!input.checkValidity()) {
			isValid = false;
		}
	});

	if (!isValid) {
		event.preventDefault();
		return false;
	}

	const confirmation = confirm("Sau khi lưu dữ liệu, giáo viên vui lòng trở lại phần mềm Themis và nhấn \"Cập nhật lại danh sách bài thi (F4)\" hoặc \"Nạp lại danh sách bài thi (F2)\" (nếu không thành công) để làm mới dữ liệu.");
	if (!confirmation) {
		event.preventDefault();
	}
}

function validateFloat(value, allowNegativeOne) {
	const floatValue = parseFloat(value);
	if (isNaN(floatValue)) return false;
	if (allowNegativeOne && floatValue === -1) return true;
	return floatValue > 0;
}

function validateInt(value, allowNegativeOne) {
	const intValue = parseInt(value, 10);
	if (isNaN(intValue)) return false;
	if (allowNegativeOne && intValue === -1) return true;
	return intValue >= 1;
}