(function() {
    const modal = document.getElementById('searchAutoModal');
    const openBtn = document.querySelector('.search-btn');
    const searchInfo = document.getElementById('search-info');
    
    // Поля ввода и подсказки
    const closeBtn = modal?.querySelector('.search-auto-close');
    const errorDiv = modal?.querySelector('.search-auto-error');
    
    const brandInput = document.getElementById('auto-brand-input');
    const brandSuggestions = document.getElementById('auto-brand-suggestions');
    const brandIdHidden = document.getElementById('auto-brand-id');

    const modelInput = document.getElementById('auto-model-input');
    const modelSuggestions = document.getElementById('auto-model-suggestions');
    const modelIdHidden = document.getElementById('auto-model-id');

    const yearSelect = document.getElementById('auto-year');
    const searchBtn = modal?.querySelector('.search-auto-button');

    let autoData = null;          // весь JSON
    let currentBrandModels = [];  // модели выбранной марки

    // Открыть модальное окно
    function openModal() {
        if (!modal) return;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        if (!autoData) {
            loadAutoData();
        } else {
            // Если данные уже загружены, просто очищаем поля
            resetFields();
        }
    }

    // Закрыть модальное окно
    function closeModal() {
        if (!modal) return;
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Сброс всех полей
    function resetFields() {
        brandInput.value = '';
        brandIdHidden.value = '';
        brandSuggestions.innerHTML = '';
        modelInput.value = '';
        modelIdHidden.value = '';
        modelSuggestions.innerHTML = '';
        modelInput.disabled = true;
        yearSelect.innerHTML = '<option value="">Сначала выберите модель</option>';
        yearSelect.disabled = true;
        updateSearchButtonState();
        if (errorDiv) errorDiv.textContent = '';
    }

    // Загрузка данных
    function loadAutoData() {
        const jsonUrl = searchAutoData.jsonUrl;
        fetch(jsonUrl)
            .then(response => {
                if (!response.ok) throw new Error('Ошибка загрузки данных');
                return response.json();
            })
            .then(data => {
                autoData = data;
                resetFields();
            })
            .catch(error => {
                console.error('Ошибка:', error);
                if (errorDiv) errorDiv.textContent = 'Не удалось загрузить список марок.';
            });
    }

    // Фильтрация марок по введённому тексту (по name или cyrillic-name)
    function filterBrands(query) {
        if (!autoData) return [];
        const lowerQuery = query.toLowerCase().trim();
        if (lowerQuery === '') return [];
        return autoData.filter(brand => 
            brand.name.toLowerCase().startsWith(lowerQuery) ||
            (brand['cyrillic-name'] && brand['cyrillic-name'].toLowerCase().startsWith(lowerQuery))
        ).slice(0, 10); // максимум 10 подсказок
    }

    // Фильтрация моделей текущей марки
    function filterModels(query) {
        if (!currentBrandModels.length) return [];
        const lowerQuery = query.toLowerCase().trim();
        if (lowerQuery === '') return [];
        return currentBrandModels.filter(model => 
            model.name.toLowerCase().startsWith(lowerQuery) ||
            (model['cyrillic-name'] && model['cyrillic-name'].toLowerCase().startsWith(lowerQuery))
        ).slice(0, 10);
    }

    // Отображение подсказок марок
    function showBrandSuggestions(suggestions) {
        brandSuggestions.innerHTML = '';
        if (!suggestions.length) {
            brandSuggestions.style.display = 'none';
            return;
        }
        suggestions.forEach(brand => {
            const div = document.createElement('div');
            div.classList.add('suggestion-item');
            div.textContent = `${brand.name} / ${brand['cyrillic-name']}`;
            div.addEventListener('click', () => {
                brandInput.value = brand.name;
                brandIdHidden.value = brand.id;
                brandSuggestions.style.display = 'none';
                // Загружаем модели выбранной марки
                currentBrandModels = brand.models || [];
                modelInput.disabled = false;
                modelInput.value = '';
                modelIdHidden.value = '';
                modelSuggestions.innerHTML = '';
                yearSelect.innerHTML = '<option value="">Выберите модель</option>';
                yearSelect.disabled = true;
                updateSearchButtonState();
                modelInput.focus();
            });
            brandSuggestions.appendChild(div);
        });
        brandSuggestions.style.display = 'block';
    }

    // Отображение подсказок моделей
    function showModelSuggestions(suggestions) {
        modelSuggestions.innerHTML = '';
        if (!suggestions.length) {
            modelSuggestions.style.display = 'none';
            return;
        }
        suggestions.forEach(model => {
            const div = document.createElement('div');
            div.classList.add('suggestion-item');
            div.textContent = `${model.name} / ${model['cyrillic-name']}`;
            div.addEventListener('click', () => {
                modelInput.value = model.name;
                modelIdHidden.value = model.id;
                modelSuggestions.style.display = 'none';
                // Заполняем годы
                fillYears(model);
                // Кнопка поиска останется отключена, пока не выбран год
                updateSearchButtonState();
            });
            modelSuggestions.appendChild(div);
        });
        modelSuggestions.style.display = 'block';
    }

    // Заполнение выбора годов на основе модели
    function fillYears(model) {
        yearSelect.innerHTML = '<option value="">Выберите год</option>';
        const from = model['year-from'];
        const to = model['year-to'] || new Date().getFullYear();
        for (let year = from; year <= to; year++) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        }
        yearSelect.disabled = false;
    }

    // Обработка поиска
    function handleSearch() {
        let brand = brandInput.value;
        let model = modelInput.value;
        let year = yearSelect.value;

        if (!brand || !model || !year) { 
            if (errorDiv) errorDiv.textContent = 'Пожалуйста, выберите марку, модель и год.'; 
            return; 
        }

        searchInfo.textContent = `Выбрано: ${brand}, ${model}, ${year}`;
        let url = window.location.pathname + `?brand=${encodeURIComponent(brand)}&model=${encodeURIComponent(model)}&year_auto=${year}`;
        console.log(url);
        window.location.href = url;
    }

    // Обработчики ввода
    brandInput.addEventListener('input', function() {
        const query = this.value;
        if (query.length < 1) {
            brandSuggestions.style.display = 'none';
            return;
        }
        const suggestions = filterBrands(query);
        showBrandSuggestions(suggestions);
    });

    modelInput.addEventListener('input', function() {
        if (!currentBrandModels.length) return;
        const query = this.value;
        if (query.length < 1) {
            modelSuggestions.style.display = 'none';
            return;
        }
        const suggestions = filterModels(query);
        showModelSuggestions(suggestions);
    });

    // Обработчик изменения года
    yearSelect.addEventListener('change', function() {
        updateSearchButtonState();
    });

    // Проверка доступности кнопки поиска
    function updateSearchButtonState() {
        const brand = brandIdHidden.value;
        const model = modelIdHidden.value;
        const year = yearSelect.value;
        searchBtn.disabled = !(brand && model && year);
    }


    // Скрывать подсказки при клике вне
    document.addEventListener('click', function(e) {
        if (!brandInput.contains(e.target) && !brandSuggestions.contains(e.target)) {
            brandSuggestions.style.display = 'none';
        }
        if (!modelInput.contains(e.target) && !modelSuggestions.contains(e.target)) {
            modelSuggestions.style.display = 'none';
        }
    });

    // Закрытие модалки
    openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // Кнопка поиска
    if (searchBtn) searchBtn.addEventListener('click', handleSearch);

    // Инициализация при загрузке DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            // Ничего дополнительно, данные загрузятся при открытии
        });
    }
})();