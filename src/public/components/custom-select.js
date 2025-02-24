class CustomSelect extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    async connectedCallback() {
        const items = JSON.parse(this.getAttribute('items') || '[]');
        const labelKey = this.getAttribute('label-key') || 'label';
        const valueKey = this.getAttribute('value-key') || 'value';
        
        const selectedValue = this.getAttribute('value'); // Get initial value
    
        const style = document.createElement('style');
        style.textContent = `
            .custom-select {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 14px;
                background: white;
            }
        `;
    
        this.shadowRoot.innerHTML = `
            <select class="custom-select">
                ${items.map(item => 
                    `<option value="${item[valueKey]}" 
                        ${selectedValue === item[valueKey] ? 'selected' : ''}>
                        ${item[labelKey]}
                    </option>`
                ).join('')}
            </select>
        `;
    
        this.shadowRoot.appendChild(style);
    
        this.selectElement = this.shadowRoot.querySelector('select');
    
        this.selectElement.addEventListener('change', (event) => {
           
            this.value = event.target.value; 
            this.dispatchEvent(new CustomEvent('change', { 
                detail: event.target.value, 
                bubbles: true, 
                composed: true 
            }));
        });
    
        if (selectedValue) {
            this.selectElement.value = selectedValue; 
        }
    }
    

    get value() {
        return this.selectElement.value;
    }

    set value(val) {
        this.selectElement.value = val;
    }
}

customElements.define('custom-select', CustomSelect);

