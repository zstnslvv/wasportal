const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const uploadBtn = document.getElementById('upload-btn');
const convertBtn = document.getElementById('convert-btn');
const listBody = document.getElementById('file-list-body');
const toast = document.getElementById('toast');
const mergeInput = document.getElementById('merge-input');
const mergeBtn = document.getElementById('merge-btn');
const mergeStatus = document.getElementById('merge-status');
const mergeSeparate = document.getElementById('merge-separate');

const MAX_FILES = 20;
const MAX_TOTAL_SIZE = 200 * 1024 * 1024;

const mapping = {
  xls: ['docx', 'pdf'],
  xlsx: ['docx', 'pdf'],
  doc: ['pdf'],
  docx: ['pdf'],
  ppt: ['pdf'],
  pptx: ['pdf'],
  png: ['pdf'],
  jpg: ['pdf'],
  jpeg: ['pdf'],
  pdf: ['xlsx', 'txt'],
  txt: ['xlsx'],
  csv: ['xlsx']
};

let filesState = [];

const showToast = (message) => {
  toast.textContent = message;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3000);
};

const formatSize = (size) => {
  if (size < 1024) return `${size} B`;
  if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`;
  return `${(size / (1024 * 1024)).toFixed(1)} MB`;
};

const createOption = (value) => {
  const option = document.createElement('option');
  option.value = value;
  option.textContent = value.toUpperCase();
  return option;
};

const renderList = () => {
  listBody.textContent = '';
  filesState.forEach((item, index) => {
    const row = document.createElement('div');
    row.className = 'file-row';

    const name = document.createElement('span');
    name.textContent = item.file.name;

    const size = document.createElement('span');
    size.textContent = formatSize(item.file.size);

    const ext = document.createElement('span');
    ext.textContent = item.ext.toUpperCase();

    const status = document.createElement('span');
    status.textContent = item.status;

    const progressWrap = document.createElement('div');
    progressWrap.className = 'progress';
    const bar = document.createElement('div');
    bar.className = 'progress-bar';
    bar.style.width = `${item.progress}%`;
    progressWrap.appendChild(bar);

    const actions = document.createElement('div');
    actions.className = 'actions-cell';

    const select = document.createElement('select');
    select.className = 'select';
    const options = mapping[item.ext] || [];
    options.forEach((option) => select.appendChild(createOption(option)));
    if (item.target) {
      select.value = item.target;
    } else if (options.length > 0) {
      select.value = options[0];
      item.target = select.value;
    }
    select.addEventListener('change', () => {
      item.target = select.value;
    });

    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'btn small';
    deleteBtn.textContent = 'Удалить';
    deleteBtn.addEventListener('click', () => {
      filesState = filesState.filter((_, idx) => idx !== index);
      renderList();
    });

    const downloadBtn = document.createElement('button');
    downloadBtn.className = 'btn small';
    downloadBtn.textContent = 'Скачать';
    downloadBtn.disabled = !item.downloadUrl;
    downloadBtn.addEventListener('click', () => {
      if (item.downloadUrl) {
        window.location.href = item.downloadUrl;
      }
    });

    actions.appendChild(select);
    actions.appendChild(deleteBtn);
    actions.appendChild(downloadBtn);

    row.appendChild(name);
    row.appendChild(size);
    row.appendChild(ext);
    row.appendChild(status);
    row.appendChild(progressWrap);
    row.appendChild(actions);

    listBody.appendChild(row);
  });
};

const validateFiles = (newFiles) => {
  if (filesState.length + newFiles.length > MAX_FILES) {
    showToast('Максимум 20 файлов за раз.');
    return false;
  }
  const total = filesState.reduce((sum, item) => sum + item.file.size, 0) +
    newFiles.reduce((sum, file) => sum + file.size, 0);
  if (total > MAX_TOTAL_SIZE) {
    showToast('Суммарный размер файлов не должен превышать 200MB.');
    return false;
  }
  return true;
};

const addFiles = (fileList) => {
  const newFiles = Array.from(fileList).map((file) => {
    const ext = file.name.split('.').pop().toLowerCase();
    return {
      file,
      ext,
      status: 'ожидает',
      progress: 0,
      fileId: null,
      jobId: null,
      downloadUrl: null,
      target: null
    };
  });

  if (!validateFiles(newFiles)) {
    return;
  }

  filesState = filesState.concat(newFiles);
  renderList();
};

const handleDrop = (event) => {
  event.preventDefault();
  dropZone.classList.remove('dragover');
  if (event.dataTransfer?.files) {
    addFiles(event.dataTransfer.files);
  }
};

const uploadFile = async (item) => {
  const formData = new FormData();
  formData.append('file', item.file);
  const response = await fetch('/api/upload', {
    method: 'POST',
    body: formData
  });
  if (!response.ok) {
    throw new Error('Ошибка загрузки');
  }
  const data = await response.json();
  item.fileId = data.file_id;
  item.status = 'загружен';
  item.progress = 20;
};

const convertFile = async (item) => {
  const response = await fetch('/api/convert', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      file_id: item.fileId,
      target_format: item.target
    })
  });
  if (!response.ok) {
    throw new Error('Ошибка конвертации');
  }
  const data = await response.json();
  item.jobId = data.job_id;
  item.status = 'в очереди';
};

const pollJob = async (item) => {
  if (!item.jobId) return;
  const response = await fetch(`/api/job/${item.jobId}`);
  if (!response.ok) {
    return;
  }
  const data = await response.json();
  item.status = data.status || item.status;
  item.progress = data.progress ?? item.progress;
  if (data.download_url) {
    item.downloadUrl = data.download_url;
  }
};

const pollJobs = async () => {
  await Promise.all(filesState.map((item) => pollJob(item)));
  renderList();
};

uploadBtn.addEventListener('click', async () => {
  for (const item of filesState) {
    if (!item.fileId) {
      try {
        item.status = 'загрузка';
        renderList();
        await uploadFile(item);
      } catch (error) {
        item.status = 'ошибка';
        showToast(error.message);
      }
    }
  }
  renderList();
});

convertBtn.addEventListener('click', async () => {
  for (const item of filesState) {
    if (!item.fileId) {
      showToast('Сначала загрузите файлы.');
      return;
    }
    if (!item.target) {
      showToast('Выберите формат конвертации.');
      return;
    }
  }

  for (const item of filesState) {
    if (!item.jobId) {
      try {
        item.status = 'запуск';
        renderList();
        await convertFile(item);
      } catch (error) {
        item.status = 'ошибка';
        showToast(error.message);
      }
    }
  }
  renderList();
});

mergeBtn.addEventListener('click', async () => {
  const files = Array.from(mergeInput.files || []);
  if (files.length === 0) {
    showToast('Выберите изображения для объединения.');
    return;
  }

  const formData = new FormData();
  files.forEach((file) => formData.append('files[]', file));
  formData.append('separate_pages', mergeSeparate.checked ? 'true' : 'false');

  mergeStatus.textContent = 'Отправка...';

  try {
    const response = await fetch('/api/merge-images-to-pdf', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error('Ошибка сборки');
    }
    const data = await response.json();
    mergeStatus.textContent = 'Задача запущена, ожидайте...';
    await pollMergeJob(data.job_id);
  } catch (error) {
    mergeStatus.textContent = 'Ошибка.';
    showToast(error.message);
  }
});

const pollMergeJob = async (jobId) => {
  const interval = setInterval(async () => {
    const response = await fetch(`/api/job/${jobId}`);
    if (!response.ok) {
      return;
    }
    const data = await response.json();
    mergeStatus.textContent = `Статус: ${data.status} (${data.progress}%)`;
    if (data.status === 'done' && data.download_url) {
      clearInterval(interval);
      const link = document.createElement('a');
      link.href = data.download_url;
      link.textContent = 'Скачать PDF';
      link.className = 'download-link';
      mergeStatus.textContent = '';
      mergeStatus.appendChild(link);
    }
    if (data.status === 'error') {
      clearInterval(interval);
      mergeStatus.textContent = data.error_message || 'Ошибка.';
    }
  }, 2000);
};

fileInput.addEventListener('change', (event) => {
  if (event.target.files) {
    addFiles(event.target.files);
  }
});

['dragenter', 'dragover'].forEach((evt) => {
  dropZone.addEventListener(evt, (event) => {
    event.preventDefault();
    dropZone.classList.add('dragover');
  });
});

['dragleave', 'drop'].forEach((evt) => {
  dropZone.addEventListener(evt, (event) => {
    if (evt === 'drop') {
      handleDrop(event);
    } else {
      dropZone.classList.remove('dragover');
    }
  });
});

setInterval(pollJobs, 3000);
