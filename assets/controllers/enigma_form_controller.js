import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["mcqSection", "timelineSection", "photoSection"];

    connect() {
        console.log('Enigma form controller connected');
        // Afficher la bonne section au chargement
        this.showCorrectSection();
    }

    typeChanged(event) {
        this.showCorrectSection();
    }

    showCorrectSection() {
        const typeSelect = document.getElementById('enigma_type');
        if (!typeSelect) {
            console.error('Type select not found');
            return;
        }
        
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        const selectedLabel = selectedOption ? selectedOption.text.toLowerCase().trim() : '';
        
        console.log('Selected type:', selectedLabel);

        // Cacher toutes les sections
        const mcqSection = document.querySelector('[data-enigma-form-target="mcqSection"]');
        const timelineSection = document.querySelector('[data-enigma-form-target="timelineSection"]');
        const photoSection = document.querySelector('[data-enigma-form-target="photoSection"]');

        if (mcqSection) mcqSection.classList.add('hidden');
        if (timelineSection) timelineSection.classList.add('hidden');
        if (photoSection) photoSection.classList.add('hidden');

        // Afficher la section correspondante
        if (selectedLabel === 'mcq' && mcqSection) {
            mcqSection.classList.remove('hidden');
            console.log('Showing MCQ section');
        } else if (selectedLabel === 'timeline' && timelineSection) {
            timelineSection.classList.remove('hidden');
            console.log('Showing Timeline section');
        } else if (selectedLabel === 'photo' && photoSection) {
            photoSection.classList.remove('hidden');
            console.log('Showing Photo section');
        }
    }

    addCollectionElement(event) {
        event.preventDefault();
        
        const button = event.currentTarget;
        const section = button.closest('[data-enigma-form-target]');
        const holder = section.querySelector('[data-collection-holder]');
        
        if (!holder) {
            console.error('Collection holder not found');
            return;
        }

        const prototype = holder.dataset.prototype;
        let index = parseInt(holder.dataset.index) || 0;
        
        // Remplacer __name__ par l'index
        const newForm = prototype.replace(/__name__/g, index);
        holder.dataset.index = index + 1;

        // Créer le nouvel élément
        const item = document.createElement('div');
        item.classList.add('relative', 'bg-gray-50', 'p-4', 'rounded', 'mb-4', 'border', 'border-gray-200');
        item.innerHTML = newForm;
        
        // Ajouter le bouton supprimer
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'absolute top-2 right-2 text-red-600 hover:text-red-800';
        deleteBtn.innerHTML = 'Supprimer <i class="fi fi-rs-trash"></i>';
        deleteBtn.setAttribute('data-action', 'enigma-form#removeCollectionElement');
        item.appendChild(deleteBtn);
        
        holder.appendChild(item);
        console.log('Added new element, index:', index);
    }

    removeCollectionElement(event) {
        event.preventDefault();
        const item = event.currentTarget.closest('div.relative');
        if (item) {
            item.remove();
            console.log('Removed element');
        }
    }
}
