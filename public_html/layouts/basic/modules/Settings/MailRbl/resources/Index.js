/* {[The file is published on the basis of YetiForce Public License 3.0 that can be found in the following directory: licenses/LicenseEN.txt or yetiforce.com]} */
'use strict';

jQuery.Class(
	'Settings_MailRbl_Index_Js',
	{},
	{
		dataTable: false,
		dataTableMap: {
			request: {
				order: [[0, 'desc']],
				columns: [
					{ data: 'datetime' },
					{ orderable: false, data: 'sender' },
					{ orderable: false, data: 'recipient' },
					{ data: 'type' },
					{ data: 'status' },
					{ data: 'user' },
					{
						orderable: false,
						data: function (row) {
							let action = '';
							action += `<button type="button" class="btn btn-secondary btn-sm js-details" data-id="${row['id']}" data-status="2" title="${app.vtranslate(
								'BTN_SHOW_DETAILS'
							)}" data-js="click"><span class="fas fa-search-plus"></span></button>`;
							action += `<button type="button" class="btn btn-primary btn-sm ml-2 js-send" data-id="${row['id']}" data-status="2" title="${app.vtranslate(
								'BTN_STATUS_ACTION_SEND_REQUEST'
							)}" data-js="click"><span class="fas fa-paper-plane"></span></button>`;
							if (row['statusId'] !== 1) {
								action += `<button type="button" class="btn btn-success btn-sm ml-2 js-update" data-id="${row['id']}" data-status="1" title="${app.vtranslate(
									'BTN_STATUS_ACTION_ACCEPT'
								)}" data-js="click"><span class="fas fa-check"></span></button>`;
							}
							if (row['statusId'] !== 2) {
								action += `<button type="button" class="btn btn-warning btn-sm ml-2 js-update" data-id="${row['id']}" data-status="2" title="${app.vtranslate(
									'BTN_STATUS_ACTION_REJECT'
								)}" data-js="click"><span class="fas fa-times"></span></button>`;
							}
							action += `<button type="button" class="btn btn-danger btn-sm ml-2 js-trash" data-id="${row['id']}" data-status="2" title="${app.vtranslate(
								'BTN_SHOW_DETAILS'
							)}" data-js="click"><span class="fas fa-trash"></span></button>`;
							return action;
						},
						defaultContent: ''
					}
				]
			},
			blackList: {
				columns: [
					{ data: 'ip' },
					{ data: 'status' },
					{ data: 'headers', orderable: false },
					{
						orderable: false,
						data: function (row) {
							let action = '';
							if (row['statusId'] !== 0) {
								action += `<button type="button" class="btn btn-success btn-sm js-update" data-id="${row['id']}" data-status="0" title="${app.vtranslate(
									'BTN_STATUS_ACTION_ACCEPT'
								)}" data-js="click"><span class="fas fa-check"></span></button>`;
							}
							if (row['statusId'] !== 1) {
								action += `<button type="button" class="btn btn-warning btn-sm ml-2 js-update" data-id="${row['id']}" data-status="2" title="${app.vtranslate(
									'BTN_UNLOCK_STATUS_ACTION_REJECT'
								)}" data-js="click"><span class="fas fa-times"></span></button>`;
							}
							action += `<button type="button" class="btn btn-danger btn-sm ml-2 js-trash" data-id="${row['id']}" data-status="2" title="${app.vtranslate(
								'BTN_SHOW_DETAILS'
							)}" data-js="click"><span class="fas fa-trash"></span></button>`;
							return action;
						},
						defaultContent: ''
					}
				]
			},
			whiteList: {
				columns: [
					{ data: 'ip' },
					{ data: 'status' },
					{ data: 'headers', orderable: false },
					{
						orderable: false,
						data: function (row) {
							let action = '';
							if (row['statusId'] !== 0) {
								action += `<button type="button" class="btn btn-success btn-sm js-update" data-id="${row['id']}" data-status="0" title="${app.vtranslate(
									'BTN_STATUS_ACTION_ACCEPT'
								)}" data-js="click"><span class="fas fa-check"></span></button>`;
							}
							if (row['statusId'] !== 1) {
								action += `<button type="button" class="btn btn-warning btn-sm ml-2 js-update" data-id="${row['id']}" data-status="2" title="${app.vtranslate(
									'BTN_UNLOCK_STATUS_ACTION_REJECT'
								)}" data-js="click"><span class="fas fa-times"></span></button>`;
							}
							action += `<button type="button" class="btn btn-danger btn-sm ml-2 js-trash" data-id="${row['id']}" data-status="2" title="${app.vtranslate(
								'BTN_SHOW_DETAILS'
							)}" data-js="click"><span class="fas fa-trash"></span></button>`;
							return action;
						},
						defaultContent: ''
					}
				]
			},
			publicRbl: {
				columns: [
					{ data: 'ip' },
					{ data: 'status' },
					{
						orderable: false,
						defaultContent: ''
					}
				]
			}
		},
		/**
		 * Register DataTable
		 */
		registerDataTable: function (container) {
			let table = container.find('.js-data-table');
			let mode = container.attr('id');
			if (table.hasClass('dataTable')) {
				table.DataTable().clear().destroy();
			}
			this.dataTable = app.registerDataTables(
				table,
				Object.assign(
					{
						processing: true,
						serverSide: true,
						paging: true,
						searching: false,
						lengthChange: false,
						pageLength: 15,
						ajax: {
							url: 'index.php?parent=Settings&module=MailRbl&action=GetData&mode=' + mode,
							type: 'POST'
						},
						order: []
					},
					this.dataTableMap[mode]
				)
			);
			return table;
		},
		/**
		 * Register tab events
		 * @param {jQuery} contentContainer
		 */
		registerTabEvents: function (contentContainer = $('.js-tab.active')) {
			const self = this;
			let mode = contentContainer.attr('id');
			let table = this.registerDataTable(contentContainer);
			table.off('click', '.js-details').on('click', '.js-details', function () {
				let progressIndicatorElement = jQuery.progressIndicator();
				app.showModalWindow(null, 'index.php?module=MailRbl&parent=Settings&view=DetailModal&mode=' + mode + '&id=' + this.dataset.id, function (container) {
					progressIndicatorElement.progressIndicator({ mode: 'hide' });
					container.find('iframe').each(function () {
						let iframe = $(this);
						iframe.on('load', (e) => {
							let content = iframe.contents();
							iframe.height(content.find('body').height() + 30);
							content.find('head').append('<style>body{margin: 0;}p{margin: 0.5em 0;}</style>');
						});
					});
				});
			});
			table.off('click', '.js-trash').on('click', '.js-trash', function () {
				AppConnector.request({
					module: app.getModuleName(),
					parent: app.getParentModuleName(),
					mode: mode,
					action: 'DeleteAjax',
					record: this.dataset.id
				}).done(function () {
					self.dataTable.ajax.reload();
				});
			});
		},
		/**
		 * Register events
		 */
		registerEvents: function () {
			this.registerTabEvents();
			$('#tabs a[data-toggle="tab"]').on('shown.bs.tab', (_) => {
				this.registerTabEvents();
			});
		}
	}
);
