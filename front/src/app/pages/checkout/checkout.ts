import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { OrderService } from '../../services/order.service';

@Component({
  selector: 'app-checkout',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './checkout.html',
  styleUrl: './checkout.css',
})
export class Checkout {
  paymentMethod = 'card';

  orderItems = [
    {
      name: 'ABC Dry Powder',
      details: '6kg • Portable',
      qty: 2,
      price: 60,
      total: 120,
      image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=100&q=80'
    },
    {
      name: 'Foam Extinguisher',
      details: '9 Litre • Wheeled',
      qty: 1,
      price: 85,
      total: 85,
      image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=100&q=80'
    }
  ];

  subtotal = 205.00;
  taxes = 20.50;
  deliveryFee = 15.00;
  grandTotal = 240.50;

  constructor(private router: Router, private orderService: OrderService) { }

  placeOrder() {
    this.orderService.placeOrder({
      companyName: 'TechSafe Industries Ltd.',
      productNames: this.orderItems.map(i => i.name).join(', '),
      category: 'Fire Safety',
      amount: this.grandTotal,
      status: 'Pending Approval'
    });
    alert('Order placed successfully! Waiting for admin approval.');
    this.router.navigate(['/dashboard']);
  }

  backToCart() {
    this.router.navigate(['/cart']);
  }
}
