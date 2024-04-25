import { Component, Input, OnInit } from '@angular/core';
import { SalesService } from '../../services/sales.service';

@Component({
  selector: 'app-modal-detail',
  templateUrl: './modal-detail.component.html',
  styleUrls: ['./modal-detail.component.scss']
})
export class ModalDetailComponent  implements OnInit {
  @Input() customerId: string;
  @Input() date: string;

  detailSale: {
      transaksi: [{
          no_struk: string,
          subtotal: number,
          tax: number,
          voucher: number,
          discount: number,
          total: number,
      }],
      totalPerDate: number
  };

  loadingProgress: number;

  constructor(
      private reportService: SalesService
  ) {
  }

  ngOnInit() {
      this.detailSale = {
          transaksi: [{
              no_struk: '',
              subtotal: 0,
              tax: 0,
              voucher: 0,
              discount: 0,
              total: 0,
          }],
          totalPerDate: 0
      };

      this.getSaleDetail();
  }

  getSaleDetail() {
      this.loadingProgress = 0;

      this.reportService.getSaleDetail(this.customerId, this.date).subscribe({
          next: (res: any) => {
              this.detailSale = res.data;
              this.loadingProgress = 100;
          },
          error: (err: any) => {
              console.error(err);
          }
      });
  }
}
