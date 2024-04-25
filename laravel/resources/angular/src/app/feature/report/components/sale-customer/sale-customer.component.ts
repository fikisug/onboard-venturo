import { Component, OnInit } from '@angular/core';
import Swal from 'sweetalert2';
import { SalesService } from '../../services/sales.service';
import { CustomerService } from 'src/app/feature/customer/services/customer.service';
import { LandaService } from 'src/app/core/services/landa.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import * as moment from 'moment';

@Component({
  selector: 'app-sale-customer',
  templateUrl: './sale-customer.component.html',
  styleUrls: ['./sale-customer.component.scss']
})
export class SaleCustomerComponent implements OnInit {
  filter: {
    start_date: string,
    end_date: string,
    customer_id
  }

  sales = [{
    customer_name: '',
    customer_total: 0,
    transaksi: [{
      total_sales: 0
    }]
  }];

  meta: {
    dates: [],
    total_per_date: [],
    grand_total: 0
  };

  showLoading: boolean;
  customers: [];
  customerId: string;
  date: string;
  titleModal: string;

  constructor(
    private salesService: SalesService,
    private customerService: CustomerService,
    private landaService: LandaService,
    private modalService: NgbModal
  ) { }

  ngOnInit(): void {
    this.resetFilter();
    this.getCustomers();
  }

  openModal(modalId, customer, sale){
    this.customerId = customer.customer_id;
    this.date = sale.date_transaction;
    const startDate = sale.date_transaction ? moment(sale.date_transaction) : moment();
    const newDate = startDate.format('DD MMMM yyyy');

    this.titleModal = customer.customer_name + ' / ' + newDate;
    this.modalService.open(modalId, { size: 'lg', backdrop: 'static' });
  }

  resetFilter() {
    this.filter = {
      start_date: null,
      end_date: null,
      customer_id: null
    }

    this.meta = {
      dates: [],
      total_per_date: [],
      grand_total: 0
    }

    this.customerId = null;
    this.date = null;
    this.titleModal = null;

    this.showLoading = false;
  }

  reloadSales() {
    this.runFilterValidation();

    this.salesService.getSalesCustomer(this.filter).subscribe((res: any) => {
      const { data, settings } = res;
      this.sales = data;
      this.meta = settings;
    });
  }

  setFilterPeriod($event) {
    this.filter.start_date = $event.startDate;
    this.filter.end_date = $event.endDate;
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

  setFilterCustomer(customers: any) {
    let customersId = [];
    customers.forEach((val: any) => (customersId.push(val.id)));
    if (!customersId) return false;

    this.filter.customer_id = customersId.join(',');
  }

  runFilterValidation() {
    if (!this.filter.start_date || !this.filter.end_date) {
      Swal.fire({
        title: 'Terjadi Kesalahan',
        text: 'Silahkan isi periode penjualan terlebih dahulu',
        icon: 'warning',
        showCancelButton: false
      });
      throw new Error("Start and End date is required");
    }
  }

  downloadExcel() {
    this.runFilterValidation();
    let queryParams = {
      start_date: this.filter.start_date,
      end_date: this.filter.end_date,
      customer_id: this.filter.customer_id,
      is_export_excel: true
    }
    this.landaService.DownloadLink('/v1/download/sales-customer', queryParams)
  }
}
