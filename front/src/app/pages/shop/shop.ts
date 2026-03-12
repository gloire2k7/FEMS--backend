import { Component, AfterViewInit, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ProductService, Product } from '../../services/product.service';

declare const lucide: { createIcons: (opts?: { nameAttr?: string }) => void } | undefined;

@Component({
  selector: 'app-shop',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './shop.html',
  styleUrl: './shop.css',
})
export class Shop implements AfterViewInit, OnInit {

  constructor(private router: Router, private productService: ProductService) { }

  filterType = '';
  filterCapacity = '';
  filterAvailability = '';
  filterSupplier = '';
  maxPrice = 900;

  showModal = false;
  selectedProduct: any = null;
  quantity = 1;

  products: Product[] = [];

  ngOnInit() {
    this.productService.products$.subscribe(data => {
      this.products = data;
    });
  }

  currentPage = 1;
  totalPages = [1, 2, 3, 4];

  openProductModal(p: any) {
    if (p.soldOut) return;
    this.selectedProduct = p;
    this.quantity = 1;
    this.showModal = true;
  }

  closeModal() {
    this.showModal = false;
    this.selectedProduct = null;
  }

  addToCartAndGo() {
    this.showModal = false;
    this.router.navigate(['/cart']);
  }

  increaseQty() { this.quantity++; }
  decreaseQty() { if (this.quantity > 1) this.quantity--; }

  goToCart() {
    this.router.navigate(['/cart']);
  }

  ngAfterViewInit() {
    setTimeout(() => {
      if (typeof lucide !== 'undefined' && lucide.createIcons) {
        lucide.createIcons();
      }
    }, 100);
  }
}
