import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

export interface Order {
  id: string;
  companyName: string;
  productNames: string;
  category: string;
  amount: number;
  status: 'Pending Approval' | 'Complete' | 'Cancelled';
  denialReason?: string;
}

@Injectable({
  providedIn: 'root'
})
export class OrderService {
  private initialOrders: Order[] = [
    { id: 'A0B1C099', companyName: 'Kigali Heights Ltd.', productNames: 'ABC Dry Powder 6kg, CO₂ 5kg', category: 'Powder', amount: 85000, status: 'Pending Approval' },
    { id: 'A0B1C088', companyName: 'BK Arena Safety', productNames: 'Foam Extinguisher 9L x 3', category: 'Foam', amount: 120000, status: 'Pending Approval' },
    { id: 'A0B1C017', companyName: 'Rwanda Coding Academy', productNames: 'Clp-Product 1203-62', category: 'Powder', amount: 30000, status: 'Complete' },
    { id: 'A0B1C061', companyName: 'Muhabura TSS', productNames: 'Clp-Product 1203-62', category: 'CO₂', amount: 50000, status: 'Cancelled', denialReason: 'Out of Stock' },
    { id: 'A0B1C049', companyName: 'Kanombe Airport', productNames: 'Clp-Product 1203-62', category: 'Water', amount: 50000, status: 'Complete' },
    { id: 'A0B1C029', companyName: 'GS Nyabihu', productNames: 'Clp-Product 1203-62', category: 'Sand', amount: 1000000, status: 'Complete' },
  ];

  private ordersSubject = new BehaviorSubject<Order[]>(this.initialOrders);
  orders$ = this.ordersSubject.asObservable();

  placeOrder(order: Partial<Order>) {
    const newOrder: Order = {
      id: order.id || 'A0B1C0' + Math.floor(Math.random() * 100),
      companyName: order.companyName || 'Unknown Client',
      productNames: order.productNames || 'Mixed Items',
      category: order.category || 'Mixed',
      amount: order.amount || 0,
      status: 'Pending Approval',
      ...order
    };
    
    // Add to top of list
    this.ordersSubject.next([newOrder, ...this.ordersSubject.value]);
  }

  approveOrder(id: string) {
    const currentOrders = this.ordersSubject.value;
    const updated = currentOrders.map(o => o.id === id ? { ...o, status: 'Complete' as const } : o);
    this.ordersSubject.next(updated);
  }

  cancelOrder(id: string, reason?: string) {
    const currentOrders = this.ordersSubject.value;
    const updated = currentOrders.map(o => o.id === id ? { ...o, status: 'Cancelled' as const, denialReason: reason || 'No reason provided' } : o);
    this.ordersSubject.next(updated);
  }
}
