class SimpleTable extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: 'open' });
    this.uniqueIDs = [];
    this.slotHeaders = [];
    this.data = [];
    this.headers = [];
    this.actionEnabled = false;
    this.actionText = "Bet!";
    this.uniqueColumn = "id";
    this.contentAttributes = {};
  }

  static get observedAttributes() {
    return ['data'];
  }

  attributeChangedCallback(name, oldValue, newValue) {
    if (name === 'data' && oldValue !== newValue) {
      this.data = JSON.parse(newValue);
      this.render();
    }
  }

  connectedCallback() {
    this.headers = JSON.parse(this.getAttribute('headers'));
    this.data = JSON.parse(this.getAttribute('data'));
    this.actionEnabled = this.getAttribute('action') === 'true';
    this.actionText = this.getAttribute('action-text') || 'Bet!';
    this.uniqueColumn = this.getAttribute('unique-column') || 'id';

    // Store slot attributes
    this.contentAttributes = {};
    this.headers.forEach(header => {
      const contentAttr = this.getAttribute(`slot-${header.value}`);
      if (contentAttr) {
        this.slotHeaders.push(header.value);
        this.contentAttributes[header.value] = contentAttr;
      }
    });

    // Listen for custom update events
    this.addEventListener('update-data', (event) => {
      this.data = event.detail;
      this.setAttribute('data', JSON.stringify(this.data)); // Update attribute for reactivity
    });

    this.render();
  }

  render() {
    this.shadowRoot.innerHTML = ''; // Clear existing content

    const style = document.createElement('style');
    style.textContent = `
      table {
          width: 100%;
          border-collapse: collapse;
      }
      th, td {
          padding: 10px;
          text-align: left;
          border: 1px solid #ddd;
      }
      th {
          background-color: #f4f4f4;
      }
      input {
          width: 100px;
          padding: 10px;
          border: 1px solid #ccc;
          border-radius: 5px;
          font-size: 14px;
      }
    `;
    this.shadowRoot.appendChild(style);

    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const tbody = document.createElement('tbody');

    let headerRow = document.createElement('tr');
    this.headers.forEach(header => {
      let th = document.createElement('th');
      th.textContent = header.text;
      headerRow.appendChild(th);
    });

    if (this.actionEnabled) {
      let th = document.createElement('th');
      th.textContent = 'Action';
      headerRow.appendChild(th);
    }

    thead.appendChild(headerRow);

    this.data.forEach((row) => {
      let tableRow = document.createElement('tr');

      this.headers.forEach(header => {
        let td = document.createElement('td');

        if (this.contentAttributes[header.value]) {
          let uniqueID = `${header.value}-${row[this.uniqueColumn]}`;
          td.innerHTML = String(this.contentAttributes[header.value]).replace('{id}', `id="${uniqueID}"`);
        } else {
          td.textContent = row[header.value] || '';
        }

        tableRow.appendChild(td);
      });

      if (this.actionEnabled) {
        let actionCell = document.createElement('td');
        const actionButton = document.createElement('button');
        actionButton.classList.add('button-link');
        actionButton.style = "padding: 10px 15px;background-color: #007BFF;color: white;border: none;border-radius: 5px;cursor: pointer;font-size: 14px;";
        actionButton.innerHTML = this.actionText;

        actionButton.addEventListener('click', () => {
          this.handleActionClick(row);
        });

        actionCell.appendChild(actionButton);
        tableRow.appendChild(actionCell);
      }

      tbody.appendChild(tableRow);
    });

    table.appendChild(thead);
    table.appendChild(tbody);
    this.shadowRoot.appendChild(table);
  }

  handleActionClick(row) {
    this.slotHeaders.forEach((header) => {
      const element = this.shadowRoot.getElementById(`${header}-${row[this.uniqueColumn]}`);
      if (element) {
        row[header] = element.value;
      }
    });

    this.dispatchEvent(new CustomEvent('action-clicked', {
      detail: row,
      bubbles: true,
      composed: true
    }));
  }
}

customElements.define('simple-table', SimpleTable);







  