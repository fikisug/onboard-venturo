import { Component, OnInit, ViewChild } from '@angular/core';
import { DataTableDirective } from 'angular-datatables';
import { DiscountService } from '../../services/discount.service';
import { CustomerService } from 'src/app/feature/customer/services/customer.service';
import { PromoService } from '../../../services/promo.service';
import { toArray } from 'rxjs';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-list-discount',
  templateUrl: './list-discount.component.html',
  styleUrls: ['./list-discount.component.scss']
})
export class ListDiscountComponent implements OnInit {

  @ViewChild(DataTableDirective)
  dtElement: DataTableDirective;
  dtInstance: Promise<DataTables.Api>;
  dtOptions: any;

  customerId: string;
  totalDiscounts = [];
  showLoading: boolean;
  listDiscount: any;
  thDiscount: any;
  listCustomer: any;
  titleForm: string;
  voucherId: string;
  showForm: boolean;
  customers: [];
  filter: {
    id: any,
  };

  constructor(
    private discountService: DiscountService,
    private customerService: CustomerService,
    private promoService: PromoService,
    private modalService: NgbModal
  ) { }

  ngOnInit(): void {
    this.showForm = false;
    this.setDefaultFilter();
    this.getDiscount();
    this.getCustomer();
    this.getCustomers();
  }

  setDefaultFilter() {
    this.filter = {
      id: '',
    }
  }

  updateCustomer(modalId, customerId) {
    this.customerId = customerId;
    this.modalService.open(modalId, { size: 'lg', backdrop: 'static' });
  }

  calculateTotalDiscounts() {
    this.listDiscount.forEach(promo => {
      this.totalDiscounts[promo.id] = 0;
    });

    this.listCustomer.forEach(customer => {
      customer.discount.forEach(discount => {
        if (this.isPromoApplied(customer.discount, discount.promo_id)) {
          this.totalDiscounts[discount.promo_id]++;
        }
      });
    });
  }

  isPromoApplied(customerDiscounts: Array<{ promo_id: string }>, promoId: string): boolean {
    return customerDiscounts.some(discount => discount.promo_id === promoId);
  }

  onPromoCheckboxChange(event: any, customer: any, promoId: string): void {
    if (event.target.checked) {
      this.createDiscount(customer.id, promoId);
    } else {
      for (let diskon of customer.discount) {
        if (diskon.promo_id == promoId) {
          this.deleteDiscount(diskon.id);
          break;
        }
      }
    }
  }

  createDiscount(customerId: string, promoId: string): void {
    const payload = { customer_id: customerId, promo_id: promoId };
    this.discountService.createDiscount(payload).subscribe({
      next: (res) => {
        this.reloadDataTable();
      },
      error: (err) => {
        console.error('Error', err);
      }
    });
  }

  deleteDiscount(diskonId: string): void {
    this.discountService.deleteDiscount(diskonId).subscribe({
      next: (res) => {
        this.reloadDataTable();
      },
      error: (err) => {
        console.error('Error', err);
      }
    });
  }

  getDiscount() {
    this.promoService.getPromo({ name: name, status: "Diskon" }).subscribe((res: any) => {
      this.listDiscount = res.data.list;
      this.thDiscount = res.data.list;
      this.showLoading = false;
    }, err => {
      console.log(err);
    });
  }

  getCustomer() {
    this.dtOptions = {
      serverSide: true,
      processing: true,
      ordering: false,
      pageLength: 25,
      ajax: (dtParams: any, callback) => {
        const params = {
          ...this.filter,
          per_page: dtParams.length,
          page: (dtParams.start / dtParams.length) + 1,
        };

        this.customerService.getUsers(params).subscribe((res: any) => {
          const { list, meta } = res.data;

          let number = dtParams.start + 1;
          list.forEach(val => (val.no = number++));
          this.listCustomer = list;
          this.calculateTotalDiscounts();
          
          callback({
            recordsTotal: meta.total,
            recordsFiltered: meta.total,
            data: [],
          });

        }, (err: any) => {

        });
      },
    };
  }

  getCustomers(name = '') {
    this.showLoading = true;
    this.customerService.getUsers({ name: name }).subscribe((res: any) => {
      this.customers = res.data.list;
      this.showLoading = false;
    }, err => {
      console.log(err);
    });
  }

  reloadDataTable(): void {
    this.dtElement.dtInstance.then((dtInstance: DataTables.Api) => {
      dtInstance.draw();
    });
  }

  filterByCustomer(customers) {
    let customersId = [];
    customers.forEach(val => (customersId.push(val.id)));
    if (!customersId) return false;

    this.filter.id = customersId.join(',');
    this.reloadDataTable();
  }


  formCreate() {
    this.showForm = true;
    this.titleForm = 'Tambah Voucher';
    this.voucherId = '';
  }

  formUpdate(voucher) {
    this.showForm = true;
    this.titleForm = 'Edit Voucher: ' + voucher.customer_name;
    this.voucherId = voucher.id;
  }

}
