import { Component, OnInit, ViewChild } from '@angular/core';
import { CustomerService } from '../../services/customer.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { DataTableDirective } from 'angular-datatables';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-list-customer',
  templateUrl: './list-customer.component.html',
  styleUrls: ['./list-customer.component.scss']
})
export class ListCustomerComponent implements OnInit{
  constructor(
    private userService: CustomerService,
    private modalService: NgbModal,
    // public ngProgress: NgProgress,
  ) {
    // this.progressRef = this.ngProgress.ref();
  }


  @ViewChild(DataTableDirective)
  dtElement: DataTableDirective;
  dtInstance: Promise<DataTables.Api>;
  dtOptions: any;



  listUser: any[] = [];
  titleModal: string;
  userId: string;

  // progressRef: NgProgressRef;

  isDisabledForm: boolean = false;
  isLoading: boolean = false;

  filter: {
    name: '',
    is_verified: ''
  };
 

  // onStarted() {
  //   this.progressRef.start();
  // }

  // onCompleted() {
  //   this.progressRef.complete();
  // }

  
reloadDataTable(): void {
  // console.log(this.filter);
  this.dtElement.dtInstance.then((dtInstance: DataTables.Api) => {
    dtInstance.draw();
  });
 } 

  
getUser() {
  this.dtOptions = {
    serverSide: true,
    processing: true,
    ordering: false,  
    pageLength: 3,
    ajax: (dtParams: any, callback) => {
      const params = {
        ...this.filter,
        per_page: dtParams.length,
        page: (dtParams.start / dtParams.length) + 1,
      };
      this.userService.getUsers(params).subscribe((res: any) => {
        const { list, meta } = res.data;
 
        let number = dtParams.start + 1;
        list.forEach(val => {
          val.no = number++;
        });
        this.listUser = list;
 
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
 

  createUser(modalId) {
    this.titleModal = 'Tambah Customer';
    this.userId = '';
    this.modalService.open(modalId, { size: 'lg', backdrop: 'static' });
  }

  updateUser(modalId, user) {
    this.titleModal = 'Edit Customer: ' + user.name;
    this.userId = user.id;
    this.modalService.open(modalId, { size: 'lg', backdrop: 'static' });
  }

  deleteUser(userId) {
    Swal.fire({
      title: 'Apakah kamu yakin ?',
      text: 'Customer akan hilang setelah kamu menghapus datanya',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#34c38f',
      cancelButtonColor: '#f46a6a',
      confirmButtonText: 'Ya, Hapus data ini !',
    }).then((result) => {
      if (!result.value) return false;
      this.userService.deleteUser(userId).subscribe((res: any) => {
        this.reloadDataTable();
      });
    });
  }

  setDefault() {
    this.filter = {
      name: '',
      is_verified: ''
    }
  }
 
  ngOnInit(): void {
    this.setDefault();
    this.getUser();
  }
}
