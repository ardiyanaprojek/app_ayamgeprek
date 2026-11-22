import './bootstrap';
import 'bootstrap';

const logoutConfirmButton = document.querySelector('#confirm-logout');

logoutConfirmButton?.addEventListener('click', () => {
    document.querySelector('#logout-form')?.requestSubmit();
});

const sidebar = document.querySelector('#app-sidebar');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const sidebarBackdrop = document.querySelector('.sidebar-backdrop');

function setSidebarOpen(isOpen) {
    sidebar?.classList.toggle('is-open', isOpen);
    sidebarBackdrop?.classList.toggle('is-visible', isOpen);
    sidebarToggle?.setAttribute('aria-expanded', String(isOpen));
    document.body.classList.toggle('sidebar-open', isOpen);
}

sidebarToggle?.addEventListener('click', () => {
    setSidebarOpen(!sidebar?.classList.contains('is-open'));
});

sidebarBackdrop?.addEventListener('click', () => setSidebarOpen(false));

sidebar?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => setSidebarOpen(false));
});

window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setSidebarOpen(false);
    }
});

// modal tambah, edit bahan baku
const ingredientModal = document.querySelector('#ingredientModal');

ingredientModal?.addEventListener('show.bs.modal', (event) => {
    const trigger = event.relatedTarget;
    const form = ingredientModal.querySelector('#ingredient-form');
    const method = ingredientModal.querySelector('#ingredient-method');
    const title = ingredientModal.querySelector('#ingredientModalLabel');
    const name = ingredientModal.querySelector('#ingredient-name');
    const unit = ingredientModal.querySelector('#ingredient-unit');
    const minimum = ingredientModal.querySelector('#ingredient-minimum');
    const isEdit = trigger?.dataset.mode === 'edit';

    form.action = isEdit ? form.dataset.updateUrlTemplate.replace('__INGREDIENT__', encodeURIComponent(trigger.dataset.id)) : form.dataset.storeUrl;
    method.value = isEdit ? 'PUT' : 'POST';
    title.textContent = isEdit ? 'Edit Bahan Baku' : 'Tambah Bahan Baru';
    name.value = isEdit ? trigger.dataset.name : '';
    unit.value = isEdit ? trigger.dataset.unit : '';
    minimum.value = isEdit ? trigger.dataset.minimum : '0';
});

// modal tambah, edit menu
const productModal = document.querySelector('#productModal');

productModal?.addEventListener('show.bs.modal', (event) => {
    const trigger = event.relatedTarget;
    const form = productModal.querySelector('#product-form');
    const method = productModal.querySelector('#product-method');
    const title = productModal.querySelector('#productModalLabel');
    const name = productModal.querySelector('#product-name');
    const price = productModal.querySelector('#product-price');
    const ingredient = productModal.querySelector('#product-ingredient');
    const quantity = productModal.querySelector('#product-quantity');
    const isEdit = trigger?.dataset.mode === 'edit';

    form.action = isEdit ? form.dataset.updateUrlTemplate.replace('__PRODUCT__', encodeURIComponent(trigger.dataset.id)) : form.dataset.storeUrl;
    method.value = isEdit ? 'PUT' : 'POST';
    title.textContent = isEdit ? 'Edit Menu' : 'Tambah Menu Baru';
    name.value = isEdit ? trigger.dataset.name : '';
    price.value = isEdit ? trigger.dataset.price : '';
    ingredient.value = isEdit ? trigger.dataset.ingredientId : '';
    quantity.value = isEdit ? trigger.dataset.quantity : '0';
});