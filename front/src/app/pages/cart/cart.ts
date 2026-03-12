import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './cart.html',
  styleUrl: './cart.css',
})
export class Cart {

  constructor(private router: Router) { }

  items = [
    {
      name: 'FireGuard Pro - CO₂ Series',
      type: 'Carbon Dioxide (CO₂)',
      capacity: '5kg',
      price: 120,
      qty: 2,
      image: ''
    },
    {
      name: 'ABC Dry Powder Classic',
      type: 'Dry Powder',
      capacity: '9kg',
      price: 85,
      qty: 5,
      image: ''
    },
    {
      name: 'AFFF Foam Sprayer',
      type: 'Foam',
      capacity: '6L',
      price: 95,
      qty: 1,
      image: ''
    }
  ];

  get subtotal() {
    return this.items.reduce((sum, i) => sum + i.price * i.qty, 0);
  }

  get taxes() {
    return Math.round(this.subtotal * 0.1);
  }

  get deliveryFee() { return 45; }

  get grandTotal() {
    return this.subtotal + this.taxes + this.deliveryFee;
  }

  get totalItems() {
    return this.items.reduce((sum, i) => sum + i.qty, 0);
  }

  increase(item: any) { item.qty++; }
  decrease(item: any) { if (item.qty > 1) item.qty--; }
  removeItem(index: number) { this.items.splice(index, 1); }
  clearCart() { this.items = []; }

  continueShopping() { this.router.navigate(['/shop']); }
  goToCheckOut() { this.router.navigate(['/checkout']); }
}
