import { Component, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';

declare const lucide: { createIcons: (opts?: { nameAttr?: string }) => void } | undefined;

@Component({
    selector: 'app-super-admin-reports',
    standalone: true,
    imports: [CommonModule, RouterModule, FormsModule],
    templateUrl: './super-admin-reports.html',
    styleUrls: ['./super-admin-reports.css'],
})
export class SuperAdminReports implements AfterViewInit {
    reports = [
        {
            name: 'Monthly Compliance Report - Q1 2024',
            description: 'Complete facility audit results',
            type: 'Inspection Compliance',
            typeClass: 'bg-blue-50 text-blue-600',
            iconClass: 'bg-blue-500',
            icon: 'file-text',
            date: 'Jan 15, 2024',
            time: '09:30 AM',
            author: 'Sarah Johnson',
            role: 'Safety Manager',
            authorAvatar: 'https://ui-avatars.com/api/?name=Sarah+Johnson&background=E2E8F0&color=64748B',
            format: 'PDF',
            formatClass: 'bg-[#EF4444]',
            formatIcon: 'file-text'
        },
        {
            name: 'Expired Extinguisher Inventory - 2024',
            description: 'Units requiring immediate replacement',
            type: 'Expired',
            typeClass: 'bg-red-50 text-red-500',
            iconClass: 'bg-[#EF4444]',
            icon: 'trash-2',
            date: 'Jan 12, 2024',
            time: '02:45 PM',
            author: 'Michael Chen',
            role: 'Compliance Officer',
            authorAvatar: 'https://ui-avatars.com/api/?name=Michael+Chen&background=0B1437&color=fff',
            format: 'Excel',
            formatClass: 'bg-[#10B981]',
            formatIcon: 'file-spreadsheet'
        },
        {
            name: 'Warehouse A - Location Summary',
            description: 'Equipment distribution by zone',
            type: 'Location Summary',
            typeClass: 'bg-amber-50 text-amber-600',
            iconClass: 'bg-[#F59E0B]',
            icon: 'map',
            date: 'Jan 10, 2024',
            time: '11:20 AM',
            author: 'Emily Rodriguez',
            role: 'Facility Manager',
            authorAvatar: 'https://ui-avatars.com/api/?name=Emily+Rodriguez&background=F1F5F9&color=64748B',
            format: 'PDF',
            formatClass: 'bg-[#EF4444]',
            formatIcon: 'file-text'
        }
    ];

    filters = ['All Reports', 'Inventory', 'Expired', 'Inspection', 'Location'];
    activeFilter = 'All Reports';

    ngAfterViewInit() {
        this.initIcons();
    }

    private initIcons() {
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
            setTimeout(() => lucide.createIcons(), 100);
            setTimeout(() => lucide.createIcons(), 500);
        }
    }
}
