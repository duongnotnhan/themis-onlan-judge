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

	if (name === 'Mark' || name.includes('[Mark]')) {
		if (name.includes('[Mark]') && !validateFloat(value, true)) {
			input.setCustomValidity('Điểm phải là số thực lớn hơn 0 (hoặc bằng -1 nếu dùng cài đặt chung).');
		} else if (!name.includes('[Mark]') && !validateFloat(value, false)) {
			input.setCustomValidity('Điểm chung mỗi test phải là số thực lớn hơn 0.');
		} else {
			input.setCustomValidity('');
		}
	} else if (name === 'TimeLimit' || name.includes('[TimeLimit]')) {
		if (name.includes('[TimeLimit]') && !validateFloat(value, true)) {
			input.setCustomValidity('Giới hạn thời gian phải là số thực lớn hơn 0 (hoặc bằng -1 nếu dùng cài đặt chung).');
		} else if (!name.includes('[TimeLimit]') && !validateFloat(value, false)) {
			input.setCustomValidity('Giới hạn thời gian chung mỗi test phải là số thực lớn hơn 0.');
		} else {
			input.setCustomValidity('');
		}
	} else if (name === 'MemoryLimit' || name.includes('[MemoryLimit]')) {
		if (name.includes('[MemoryLimit]') && !validateInt(value, true)) {
			input.setCustomValidity('Giới hạn bộ nhớ phải là số nguyên lớn hơn hoặc bằng 1 (hoặc bằng -1 nếu dùng cài đặt chung).');
		} else if (!name.includes('[MemoryLimit]') && !validateFloat(value, false)) {
			input.setCustomValidity('Giới hạn bộ nhớ chung mỗi test phải là số nguyên lớn hơn hoặc bằng 1.');
		} else {
			input.setCustomValidity('');
		}
	}

	input.reportValidity();
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