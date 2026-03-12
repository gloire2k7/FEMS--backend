import { Component, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, RouterLink, RouterLinkActive } from '@angular/router';
import { FormsModule } from '@angular/forms';

declare const lucide: { createIcons: (opts?: { nameAttr?: string }) => void } | undefined;

@Component({
  selector: 'app-admin-location-details',
  standalone: true,
  imports: [CommonModule, RouterModule, RouterLink, RouterLinkActive, FormsModule],
  templateUrl: './admin-location-details.html',
  styleUrl: './admin-location-details.css',
})
export class AdminLocationDetails implements AfterViewInit {
  inspectionsOpen = true;

  toggleInspections() {
    this.inspectionsOpen = !this.inspectionsOpen;
    this.initIcons();
  }
  searchExtinguisherQuery = '';

  extinguishers = [
    { id: 'FE-MH-001', type: 'CO₂', capacity: '5kg', dateRefilled: 'Jan 15, 2024', status: 'Passed', lastInspection: 'May 10, 2024', nextInspection: 'Nov 10, 2024' },
    { id: 'FE-MH-002', type: 'Dry Powder', capacity: '6kg', dateRefilled: 'Feb 20, 2024', status: 'Passed', lastInspection: 'May 12, 2024', nextInspection: 'Nov 12, 2024' },
    { id: 'FE-MH-003', type: 'Foam', capacity: '9L', dateRefilled: 'Mar 05, 2023', status: 'Almost Expired', lastInspection: 'Dec 20, 2023', nextInspection: 'Jun 20, 2024' },
    { id: 'FE-MH-004', type: 'Dry Powder', capacity: '4kg', dateRefilled: 'Jan 28, 2024', status: 'Passed', lastInspection: 'May 08, 2024', nextInspection: 'Nov 08, 2024' },
    { id: 'FE-MH-005', type: 'CO₂', capacity: '5kg', dateRefilled: 'Aug 10, 2022', status: 'Expired', lastInspection: 'Feb 15, 2023', nextInspection: 'May 01, 2024' },
    { id: 'FE-MH-006', type: 'Water', capacity: '9L', dateRefilled: 'Apr 12, 2023', status: 'Almost Expired', lastInspection: 'Jan 10, 2024', nextInspection: 'Jul 10, 2024' },
    { id: 'FE-MH-007', type: 'Dry Powder', capacity: '6kg', dateRefilled: 'Feb 18, 2024', status: 'Passed', lastInspection: 'May 15, 2024', nextInspection: 'Nov 15, 2024' },
    { id: 'FE-MH-008', type: 'Foam', capacity: '6L', dateRefilled: 'Sep 20, 2022', status: 'Expired', lastInspection: 'Mar 15, 2023', nextInspection: 'Apr 28, 2024' }
  ];

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

  getExtinguisherStatusClass(status: string) {
    switch (status) {
      case 'Passed':
        return 'bg-emerald-50 text-emerald-600 border-emerald-100';
      case 'Almost Expired':
        return 'bg-orange-50 text-orange-600 border-orange-100';
      case 'Expired':
        return 'bg-red-50 text-red-600 border-red-100';
      default:
        return 'bg-slate-50 text-slate-600 border-slate-100';
    }
  }

  getStatusIconColor(status: string) {
    switch (status) {
      case 'Passed': return 'bg-emerald-500';
      case 'Almost Expired': return 'bg-orange-500';
      case 'Expired': return 'bg-red-500';
      default: return 'bg-slate-400';
    }
  }

  getNextInspectionClass(status: string) {
    switch (status) {
      case 'Almost Expired':
        return 'text-orange-600 font-black';
      case 'Expired':
        return 'text-red-600 font-black';
      default:
        return 'text-slate-500 font-semibold';
    }
  }
}
