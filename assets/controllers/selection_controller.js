import { Controller } from '@hotwired/stimulus';
import Sortable from 'sortablejs';

export default class extends Controller {
    static values = {
        saveUrl: String
    }

    static targets = ["list", "item"]

    connect() {
        this.sortable = new Sortable(this.listTarget, {
            animation: 150,
            handle: '.handle',
            onEnd: () => this.save()
        });
    }

    save() {
        const data = {
            enigmas: this.itemTargets.map((item, index) => {
                return {
                    id: item.dataset.id,
                    order: index + 1,
                    isActive: item.querySelector('input[type="checkbox"]').checked
                };
            })
        };

        fetch(this.saveUrlValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Optional: Show a small success notification
                console.log('Saved successfully');
            }
        });
    }

    toggle(event) {
        this.save();
    }
}
