import { Component, OnInit, ViewChild } from '@angular/core';
import { SalesService } from '../../services/sales.service';
import { CustomerService } from 'src/app/feature/customer/services/customer.service';
import { ProductService } from 'src/app/feature/product/product/services/product.service';
import { DataTableDirective } from 'angular-datatables';
// import { jsPDF } from 'jspdf';
// import jsPDF from 'jspdf';
import * as jsPDF from 'jspdf';
import 'jspdf-autotable';

@Component({
  selector: 'app-sale-transaction',
  templateUrl: './sale-transaction.component.html',
  styleUrls: ['./sale-transaction.component.scss']
})
export class SaleTransactionComponent implements OnInit {

  @ViewChild(DataTableDirective)
  dtElement: DataTableDirective;
  dtInstance: Promise<DataTables.Api>;
  dtOptions: any;

  filter: {
    start_date: string,
    end_date: string,
    customer_id: string,
    menu_id: string
  }
  showLoading: boolean;
  loadingCustomer: boolean;
  customers: [];
  menus: [];
  transaction: [{
    no: 0,
    no_struk: '',
    customer_name: '',
    date: '',
    discount: '',
    voucher: '',
    product_name: '',
    total_item: '',
    price: '',
    total: '',
    total_bayar: ''
  }];

  constructor(
    private salesService: SalesService,
    private customerService: CustomerService,
    private productService: ProductService
  ) { }

  ngOnInit(): void {
    this.resetFilter();
    this.getCustomers();
    this.getMenus();
    this.getTransaction();
  }

  resetFilter() {
    this.filter = {
      start_date: null,
      end_date: null,
      customer_id: null,
      menu_id: null
    }
  }

  getCustomers(name = '') {
    this.loadingCustomer = true;
    this.customerService.getUsers({ name: name }).subscribe((res: any) => {
      this.customers = res.data.list;
      this.loadingCustomer = false;
    }, err => {
      console.log(err);
    });
  }

  getMenus(name = '') {
    this.showLoading = true;
    this.productService.getProducts({ name: name }).subscribe((res: any) => {
      this.menus = res.data.list;
      this.showLoading = false;
    }, err => {
      console.log(err);
    });
  }

  // reloadTransaction() {
  //   this.salesService.getSalesTransaction(this.filter).subscribe((res: any) => {
  //     const { data } = res;
  //     let number = 1;
  //     data.forEach(val => (val.no = number++));
  //     this.transaction = data;
  //   })
  // }

  getTransaction() {
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
        this.salesService.getSalesTransaction(params).subscribe((res: any) => {
          const { list, meta } = res.data;
          let number = dtParams.start + 1;
          list.forEach(val => (val.no = number++));
          this.transaction = list;

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

  setFilterMenu(menu) {
    this.filter.menu_id = this.generateSafeParam(menu);
    this.reloadDataTable();
  }

  generateSafeParam(list) {
    let paramId = [];
    list.forEach(val => (paramId.push(val.id)));
    if (!paramId) return '';

    return paramId.join(',')
  }

  generatePDF() {
    const doc = new jsPDF.default();

    const table = document.getElementById('data-table');

    const columnStyles = {
      0: { cellWidth: 8 },
      1: { cellWidth: 30 },
      2: { cellWidth: 20 } 
    };

    const styles = {
      fontSize: 7,
    };

    doc.text('Data Transaksi', 10, 10);
    doc.autoTable({
      html: table,
      // columnStyles: columnStyles,
      styles: styles
    });

    doc.save('transactions.pdf');
  }

  exportTableToCSV() {
    // Mengambil elemen tabel dari DOM
    const table = document.getElementById('data-table');
  
    // Mendapatkan data dari setiap sel tabel
    const rows = table.querySelectorAll('tr');
    const csvData = [];
  
    // Mendapatkan data untuk setiap baris tabel
    rows.forEach(row => {
      const rowData = [];
      row.querySelectorAll('td').forEach((cell, index) => {
        // Jika index kolom adalah angka, hapus karakter non-numeric seperti koma (,)
        if (index === 5 || index === 6 || index === 9 || index === 10) { // Index kolom yang berisi angka yang perlu diubah
          rowData.push(cell.textContent.trim().replace(/[^0-9]/g, '')); // Hapus karakter non-numeric
        } else {
          rowData.push(cell.textContent.trim());
        }
      });
      csvData.push(rowData.join(','));
    });
  
    // Membuat string CSV
    const csvString = csvData.join('\n');
  
    // Membuat objek blob
    const blob = new Blob([csvString], { type: 'text/csv' });
  
    // Membuat URL objek blob
    const url = window.URL.createObjectURL(blob);
  
    // Membuat anchor untuk mengunduh file
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'table.csv');
    link.style.visibility = 'hidden';
  
    // Menambahkan anchor ke dalam dokumen dan mengkliknya
    document.body.appendChild(link);
    link.click();
  
    // Membersihkan URL objek blob
    window.URL.revokeObjectURL(url);
    document.body.removeChild(link);
  }
  
  
}

