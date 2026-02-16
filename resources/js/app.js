import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const resetFormFields = (form) => {
	if (!form) {
		return;
	}

	const fields = form.querySelectorAll('input, select, textarea');
	fields.forEach((field) => {
		if (field.matches('[type="hidden"], [type="submit"], [type="button"], [type="reset"]')) {
			return;
		}

		if (field.type === 'checkbox' || field.type === 'radio') {
			field.checked = false;
			return;
		}

		if (field.tagName.toLowerCase() === 'select') {
			field.selectedIndex = 0;
			return;
		}

		field.value = '';
	});

	const progressWrapper = form.querySelector('[data-upload-progress]');
	const progressBar = form.querySelector('[data-upload-progress-bar]');
	const progressText = form.querySelector('[data-upload-progress-text]');
	const progressBarText = form.querySelector('[data-upload-progress-bar-text]');

	if (progressWrapper) {
		progressWrapper.classList.add('hidden');
		progressWrapper.setAttribute('aria-hidden', 'true');
	}
	if (progressBar) {
		progressBar.style.width = '0%';
		progressBar.setAttribute('aria-valuenow', '0');
		progressBar.classList.remove('bg-rose-500');
	}
	if (progressText) {
		progressText.textContent = '0%';
	}
	if (progressBarText) {
		progressBarText.textContent = '0%';
	}
};

window.resetFormById = (formId) => {
	const form = document.getElementById(formId);
	resetFormFields(form);
};

document.addEventListener('DOMContentLoaded', () => {
	const forms = document.querySelectorAll('form[data-upload-form]');
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
	const resetOnLoadForms = document.querySelectorAll('form[data-reset-on-load]');

	resetOnLoadForms.forEach((form) => resetFormFields(form));

	forms.forEach((form) => {
		form.addEventListener('submit', (event) => {
			const progressWrapper = form.querySelector('[data-upload-progress]');
			const progressBar = form.querySelector('[data-upload-progress-bar]');
			const progressText = form.querySelector('[data-upload-progress-text]');
			const progressBarText = form.querySelector('[data-upload-progress-bar-text]');
			const submitButton = form.querySelector('[data-upload-submit]');
			const submitText = form.querySelector('[data-upload-submit-text]');
			const submitTextOriginal = submitText ? submitText.textContent : null;
			const submitSpinner = form.querySelector('[data-upload-spinner]');
			const cancelButton = form.querySelector('[data-upload-cancel]');
			const modalContainer = form.closest('[data-upload-modal]');
			const closeButton = modalContainer ? modalContainer.querySelector('[data-upload-close]') : null;
			const fileInputs = form.querySelectorAll('input[type="file"]');
			const sizeError = form.querySelector('[data-upload-size-error]');
			const requiresAdminPassword = Boolean(form.querySelector('input[name="admin_password"]'));

			const hasFile = Array.from(fileInputs).some((input) => input.files && input.files.length > 0);
			const oversizedInput = Array.from(fileInputs).find((input) => {
				const maxBytes = Number(input.dataset.maxBytes || 0);
				if (!maxBytes || !input.files || input.files.length === 0) {
					return false;
				}
				return input.files[0].size > maxBytes;
			});
			const showProgress = progressWrapper && progressBar && hasFile;

			if (hasFile && !requiresAdminPassword) {
				event.preventDefault();
			}

			if (oversizedInput) {
				event.preventDefault();
				const maxLabel = oversizedInput.dataset.maxSizeLabel || '100 MB';
				if (sizeError) {
					sizeError.textContent = `File is too large. Max size is ${maxLabel}.`;
					sizeError.classList.remove('hidden');
				}
				if (submitButton) {
					submitButton.removeAttribute('disabled');
					submitButton.classList.remove('opacity-80', 'cursor-not-allowed');
				}
				if (submitSpinner) {
					submitSpinner.classList.add('hidden');
				}
				if (submitText && submitTextOriginal) {
					submitText.textContent = submitTextOriginal;
				}
				if (cancelButton) {
					cancelButton.removeAttribute('disabled');
					cancelButton.classList.remove('opacity-60', 'pointer-events-none');
				}
				if (closeButton) {
					closeButton.removeAttribute('disabled');
					closeButton.classList.remove('opacity-60', 'pointer-events-none');
				}
				return;
			}

			if (sizeError) {
				sizeError.classList.add('hidden');
				sizeError.textContent = '';
			}
			if (showProgress) {
				progressWrapper.classList.remove('hidden');
				progressWrapper.setAttribute('aria-hidden', 'false');
				progressBar.style.width = '0%';
				progressBar.setAttribute('aria-valuenow', '0');
				if (progressText) {
					progressText.textContent = '0%';
				}
				if (progressBarText) {
					progressBarText.textContent = '0%';
				}
			}

			if (submitButton) {
				submitButton.setAttribute('disabled', 'disabled');
				submitButton.classList.add('opacity-80', 'cursor-not-allowed');
			}
			if (submitSpinner) {
				submitSpinner.classList.remove('hidden');
			}
			if (submitText) {
				submitText.textContent = 'Uploading...';
			}
			if (cancelButton) {
				cancelButton.setAttribute('disabled', 'disabled');
				cancelButton.classList.add('opacity-60', 'pointer-events-none');
			}
			if (closeButton) {
				closeButton.setAttribute('disabled', 'disabled');
				closeButton.classList.add('opacity-60', 'pointer-events-none');
			}

			if (!hasFile || requiresAdminPassword) {
				return;
			}

			const xhr = new XMLHttpRequest();
			xhr.open(form.method || 'POST', form.action);
			xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			if (csrfToken) {
				xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
			}

			xhr.upload.addEventListener('progress', (e) => {
				if (!e.lengthComputable) {
					return;
				}

				if (!showProgress) {
					return;
				}

				const percent = Math.round((e.loaded / e.total) * 100);
				progressBar.style.width = `${percent}%`;
				progressBar.setAttribute('aria-valuenow', String(percent));
				if (progressText) {
					progressText.textContent = `${percent}%`;
				}
				if (progressBarText) {
					progressBarText.textContent = `${percent}%`;
				}
			});

			xhr.addEventListener('load', () => {
				if (xhr.status >= 200 && xhr.status < 400) {
					resetFormFields(form);
					const target = xhr.responseURL || window.location.href;
					window.location.href = target;
					return;
				}

				if (xhr.status === 413 && sizeError) {
					sizeError.textContent = 'Upload failed. The server upload limit is lower than 100 MB. Increase upload_max_filesize and post_max_size in php.ini.';
					sizeError.classList.remove('hidden');
				}

				if (progressText) {
					progressText.textContent = 'Upload failed';
				}
				if (progressBar) {
					progressBar.classList.add('bg-rose-500');
				}
				if (submitButton) {
					submitButton.removeAttribute('disabled');
					submitButton.classList.remove('opacity-80', 'cursor-not-allowed');
				}
				if (submitSpinner) {
					submitSpinner.classList.add('hidden');
				}
				if (submitText && submitTextOriginal) {
					submitText.textContent = submitTextOriginal;
				}
				if (cancelButton) {
					cancelButton.removeAttribute('disabled');
					cancelButton.classList.remove('opacity-60', 'pointer-events-none');
				}
				if (closeButton) {
					closeButton.removeAttribute('disabled');
					closeButton.classList.remove('opacity-60', 'pointer-events-none');
				}
			});

			xhr.addEventListener('error', () => {
				if (progressText) {
					progressText.textContent = 'Upload failed';
				}
				if (progressBar) {
					progressBar.classList.add('bg-rose-500');
				}
				if (submitButton) {
					submitButton.removeAttribute('disabled');
					submitButton.classList.remove('opacity-80', 'cursor-not-allowed');
				}
				if (submitSpinner) {
					submitSpinner.classList.add('hidden');
				}
				if (submitText && submitTextOriginal) {
					submitText.textContent = submitTextOriginal;
				}
				if (cancelButton) {
					cancelButton.removeAttribute('disabled');
					cancelButton.classList.remove('opacity-60', 'pointer-events-none');
				}
				if (closeButton) {
					closeButton.removeAttribute('disabled');
					closeButton.classList.remove('opacity-60', 'pointer-events-none');
				}
			});

			const formData = new FormData(form);
			xhr.send(formData);
		});
	});
});
