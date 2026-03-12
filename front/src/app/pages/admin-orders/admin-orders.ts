import { Component, AfterViewInit, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, RouterLink, RouterLinkActive } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { OrderService, Order } from '../../services/order.service';

declare const lucide: { createIcons: (opts?: { nameAttr?: string }) => void } | undefined;

@Component({
  selector: 'app-admin-orders',
  standalone: true,
  imports: [CommonModule, RouterModule, RouterLink, RouterLinkActive, FormsModule],
  templateUrl: './admin-orders.html',
  styleUrl: './admin-orders.css',
})
export class AdminOrders implements AfterViewInit, OnInit {
  inspectionsOpen = true;
  searchQuery = '';
  filterStatus = 'all';

  orders: Order[] = [];

  // Denial modal state
  showDenialModal = false;
  orderToDeny: Order | null = null;
  selectedReason = '';
  customReason = '';

  denialReasons = [
    { value: 'out_of_stock', label: 'Out of Stock', icon: 'package-x' },
    { value: 'payment_issue', label: 'Payment Issue', icon: 'credit-card' },
    { value: 'invalid_order', label: 'Invalid Order Details', icon: 'file-x' },
    { value: 'product_discontinued', label: 'Product Discontinued', icon: 'ban' },
    { value: 'delivery_unavailable', label: 'Delivery Unavailable in Area', icon: 'map-pin-off' },
    { value: 'other', label: 'Other (specify below)', icon: 'message-circle' },
  ];

  constructor(private orderService: OrderService) {}

  ngOnInit() {
    this.orderService.orders$.subscribe(orders => {
      this.orders = orders;
    });
  }

  toggleInspections() {
    this.inspectionsOpen = !this.inspectionsOpen;
    this.initIcons();
  }

  get pendingCount() {
    return this.orders.filter(o => o.status === 'Pending Approval').length;
  }

  get completedCount() {
    return this.orders.filter(o => o.status === 'Complete').length;
  }

  get cancelledCount() {
    return this.orders.filter(o => o.status === 'Cancelled').length;
  }

  get filteredOrders(): Order[] {
    return this.orders.filter(o => {
      const matchesSearch =
        !this.searchQuery ||
        o.id.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        o.companyName.toLowerCase().includes(this.searchQuery.toLowerCase());
      const matchesStatus =
        this.filterStatus === 'all' || o.status === this.filterStatus;
      return matchesSearch && matchesStatus;
    });
  }

  approveOrder(id: string) {
    this.orderService.approveOrder(id);
    this.initIcons();
  }

  openDenyModal(order: Order) {
    this.orderToDeny = order;
    this.selectedReason = '';
    this.customReason = '';
    this.showDenialModal = true;
    setTimeout(() => this.initIcons(), 100);
  }

  closeDenyModal() {
    this.showDenialModal = false;
    this.orderToDeny = null;
    this.selectedReason = '';
    this.customReason = '';
  }

  confirmDeny() {
    if (!this.orderToDeny) return;
    const reasonLabel = this.selectedReason === 'other'
      ? (this.customReason.trim() || 'Other reason')
      : (this.denialReasons.find(r => r.value === this.selectedReason)?.label || 'No reason provided');
    this.orderService.cancelOrder(this.orderToDeny.id, reasonLabel);
    this.closeDenyModal();
    this.initIcons();
  }

  get canConfirmDeny(): boolean {
    if (!this.selectedReason) return false;
    if (this.selectedReason === 'other' && !this.customReason.trim()) return false;
    return true;
  }

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
    [100, 300, 600, 1000, 2000].forEach(delay => setTimeout(run, delay));
  }
}
