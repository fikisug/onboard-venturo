import { Component, OnInit } from '@angular/core';
import { DashboardService } from './services/dashboard.service';
import * as moment from 'moment';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit {
  filter: {
    periodType: string,
    dateRange: {
      startDate: string,
      endDate: string
    }
  }

  total: {
    today: 0,
    yesterday: 0,
    last_month: 0,
    this_month: 0,
  }

  listYear: any[];
  selectedYear: any;

  constructor(
    private dashboardService: DashboardService
  ) {
  }

  ngOnInit(): void {
    this.resetFilter();
    this.setDefault();
    this.getSummaries();
    this.changeDiagram();
  }

  resetFilter() {
    this.filter = {
      periodType: 'year',
      dateRange: {
        startDate: '',
        endDate: '',
      }
    }
  }

  public barChartOptions: any = {
    scaleShowVerticalLines: false,
    responsive: true,
    legend: {
      display: false,
    },
    scales: {
      y: {
        ticks: {
          callback: (value: any): string => 'Rp ' + new Intl.NumberFormat('de-DE').format(value),
        },
      },
    },
    tooltips: {
      display: false,
      callbacks: {
        label: (context: any) => {
          let value = context.parsed.y;
          return 'Rp ' + new Intl.NumberFormat('de-DE').format(value);
        },
      },
    },
  };


  public barChartLabels = [];
  public barChartData = [
    { data: [], label: '', backgroundColor: '' },
  ];

  setDefault() {
    this.total = {
      today: 0,
      yesterday: 0,
      last_month: 0,
      this_month: 0,
    }

    this.selectedYear = moment(new Date()).format('YYYY');
  }

  getSummaries() {
    this.dashboardService.getSummaries().subscribe((res: any) => {
      this.total = res.data;
    });
  }

  filterByPeriod(period: any) {
    if (period.startDate && period.endDate) {
      this.filter = {
        periodType: '',
        dateRange: {
          startDate: period.startDate,
          endDate: period.endDate,
        }
      }
    } else {
      if (this.filter.periodType) {
        this.filter.dateRange = {
          startDate: '',
          endDate: '',
        }
      } else {
        this.filter = {
          periodType: 'year',
          dateRange: {
            startDate: '',
            endDate: '',
          }
        }
      }
    }
    this.changeDiagram();
  }

  changeDiagram() {
    if (this.filter.periodType == 'year') {
      this.getTotalPerYear();
    } else if (this.filter.periodType == 'month') {
      this.getTotalPerMonth();
    } else if (this.filter.dateRange.startDate && this.filter.dateRange.endDate) {
      this.getTotalPerDay();
    }
  }

  getTotalPerYear() {
    this.dashboardService.getTotalPerYear().subscribe((res: any) => {
      this.barChartLabels = res.data.label;
      this.listYear = this.barChartLabels;

      this.barChartData = [
        { data: res.data.data, label: 'Pendapatan', backgroundColor: '#C7E9ED' }
      ]
    });
  }

  getTotalPerMonth() {
    this.dashboardService.getTotalPerMonth(this.selectedYear).subscribe((res: any) => {
      this.barChartLabels = res.data.label;
      this.barChartData = [
        { data: res.data.data, label: 'Pendapatan', backgroundColor: '#C7E9ED' }
      ]
    });
  }

  getTotalPerDay() {
    this.dashboardService.getTotalPerDay(this.filter.dateRange).subscribe((res: any) => {
      this.barChartLabels = res.data.label;
      this.barChartData = [
        { data: res.data.data, label: 'Pendapatan', backgroundColor: '#C7E9ED' }
      ]
    });
  }
}
