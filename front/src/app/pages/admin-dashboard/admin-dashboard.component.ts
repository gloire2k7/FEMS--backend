import { Component, AfterViewInit, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, RouterLink, RouterLinkActive } from '@angular/router';
import { OrderService, Order } from '../../services/order.service';

declare const lucide: { createIcons: (opts?: { nameAttr?: string }) => void } | undefined;

@Component({
    selector: 'app-admin-dashboard',
    standalone: true,
    imports: [CommonModule, RouterModule, RouterLink, RouterLinkActive],
    templateUrl: './admin-dashboard.component.html',
    styleUrl: './admin-dashboard.component.css'
})
export class AdminDashboardComponent implements AfterViewInit, OnInit {
    inspectionsOpen = true;
    orders: Order[] = [];

    constructor(private orderService: OrderService) {}

    ngOnInit() {
        this.orderService.orders$.subscribe(orders => {
            this.orders = orders;
        });
    }

    approveOrder(id: string) {
        this.orderService.approveOrder(id);
    }

    cancelOrder(id: string) {
        this.orderService.cancelOrder(id);
    }

    get pendingCount() {
        return this.orders.filter(o => o.status === 'Pending Approval').length;
    }

    toggleInspections() {
        this.inspectionsOpen = !this.inspectionsOpen;
        this.initIcons();
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

        // Immediate run
        run();

        // Sequence of retries to catch any late rendering
        [100, 300, 600, 1000, 2000].forEach(delay => {
            setTimeout(run, delay);
        });
    }
}
