import { Component, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, RouterLink, RouterLinkActive, Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ProductService } from '../../services/product.service';

declare const lucide: { createIcons: (opts?: { nameAttr?: string }) => void } | undefined;

@Component({
  selector: 'app-admin-add-extinguisher',
  standalone: true,
  imports: [CommonModule, RouterModule, RouterLink, RouterLinkActive, FormsModule],
  templateUrl: './admin-add-extinguisher.html',
  styleUrl: './admin-add-extinguisher.css',
})
export class AdminAddExtinguisher implements AfterViewInit {
  inspectionsOpen = true;

  constructor(private productService: ProductService, private router: Router) {}

  toggleInspections() {
    this.inspectionsOpen = !this.inspectionsOpen;
    this.initIcons();
  }
  purchaseType: string = 'Fixed Price';

  formData = {
    title: 'Fire extinguisher',
    price: '30',
    capacity: '6kg',
    category: 'Powder',
    origin: 'China',
    currency: 'Dollars',
    description: ''
  };

  ngAfterViewInit() {
    this.initIcons();
  }

  private initIcons() {
    const run = () => {
      if (typeof lucide !== 'undefined' && lucide.createIcons) {
        lucide.createIcons();
      }
    };
    run();
    [100, 300, 600, 1000, 2000].forEach((delay) => {
      setTimeout(run, delay);
    });
  }

  setPurchaseType(type: string) {
    this.purchaseType = type;
  }

  saveUnit() {
    this.productService.addProduct({
      name: this.formData.title,
      price: parseFloat(this.formData.price),
      capacity: this.formData.capacity,
      badge: this.formData.category.toUpperCase(),
      supplier: 'Admin Upload',
      model: 'ADM-' + Math.floor(Math.random() * 10000)
    });
    alert('Extinguisher added successfully!');
    this.router.navigate(['/shop']);
  }
}
