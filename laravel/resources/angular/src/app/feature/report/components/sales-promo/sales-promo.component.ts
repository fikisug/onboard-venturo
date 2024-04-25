import { Component, OnInit, ViewChild } from '@angular/core';
import { CustomerService } from 'src/app/feature/customer/services/customer.service';
import { PromoService } from 'src/app/feature/promo/services/promo.service';
import { SalesService } from '../../services/sales.service';
import { DataTableDirective } from 'angular-datatables';

@Component({
  selector: 'app-sales-promo',
  templateUrl: './sales-promo.component.html',
  styleUrls: ['./sales-promo.component.scss']
})
export class SalesPromoComponent implements OnInit {

  @ViewChild(DataTableDirective)
  dtElement: DataTableDirective;
  dtInstance: Promise<DataTables.Api>;
  dtOptions: any;

  filter: {
    start_date: string,
    end_date: string,
    customer_id: string,
    promo_id: string
  }
  showLoading: boolean;
  customers: [];
  promos: [];
  sales: [{
    no: 0,
    customer_name: '',
    date_transaction: '',
    diskon_name: '',
    voucher_name: '',
  }];

  constructor(
    private salesService: SalesService,
    private customerService: CustomerService,
    private promoService: PromoService
  ) { }

  ngOnInit(): void {
    this.resetFilter();
    this.getCustomers();
    this.getPromos();
    this.getSales();
  }

  resetFilter() {
    this.filter = {
      start_date: null,
      end_date: null,
      customer_id: null,
      promo_id: null
    }
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

  getPromos(name = '') {
    this.showLoading = true;
    this.promoService.getPromo({ name: name }).subscribe((res: any) => {
      this.promos = res.data.list;
      this.showLoading = false;
    }, err => {
      console.log(err);
    });
  }

  // reloadSales() {
  //   this.salesService.getSalesPromo(this.filter).subscribe((res: any) => {
  //     const { data } = res;
  //     let number = 1;
  //     data.forEach(val => (val.no = number++));
  //     this.sales = data;
  //   })
  // }

  getSales() {
    this.dtOptions = {
      serverSide: true,
      processing: true,
      ordering: false,
      pageLength: 10,
      ajax: (dtParams: any, callback) => {
        const params = {
          ...this.filter,
          per_page: dtParams.length,
          page: (dtParams.start / dtParams.length) + 1,
        };
        this.salesService.getSalesPromo(params).subscribe((res: any) => {
          const { list, meta } = res.data;
          let number = dtParams.start + 1;
          list.forEach(val => (val.no = number++));
          this.sales = list;

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

  reloadDataTable(): void {
    this.dtElement.dtInstance.then((dtInstance: DataTables.Api) => {
      dtInstance.draw();
    });
  }

  setFilterPeriod($event) {
    this.filter.start_date = $event.startDate;
    this.filter.end_date = $event.endDate;
    this.reloadDataTable();
  }

  setFilterCustomer(customers) {
    this.filter.customer_id = this.generateSafeParam(customers);
    this.reloadDataTable();
  }

  setFilterPromo(promos) {
    this.filter.promo_id = this.generateSafeParam(promos);
    this.reloadDataTable();
  }

  generateSafeParam(list) {
    let paramId = [];
    list.forEach(val => (paramId.push(val.id)));
    if (!paramId) return '';

    return paramId.join(',')
  }
}

