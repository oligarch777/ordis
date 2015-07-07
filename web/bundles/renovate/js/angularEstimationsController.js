Renovate.controller('EstimationsController', function($scope,$http,$modal){
	console.log('EstimationsController loaded!');
	
	$scope.estimations = [];
	$scope.totalItems = 0;
	$scope.currentPage = 1;
	$scope.itemsPerPage = 10;
	
	$scope.filterHandler = null;
	
	$scope.filter = {
			from: null,
			to: null
	}
	
	$scope.urlsEstimationsNg = URLS.estimationsNg;
	$scope.urlsEstimationsCountNg = URLS.estimationsCountNg;
	$scope.urlsEstimationsRemoveNg = URLS.estimationsRemoveNg;
	$scope.urlsEstimationsShow = URLS.estimationsShow;
	
	$scope.$watch('itemsPerPage', function(){
		console.log("itemsPerPage => ", $scope.itemsPerPage);
		getEstimationsCount();
	});
	
	$scope.$watch('currentPage', function(){
		console.log("currentPage => ", $scope.currentPage);
		getEstimations();
	});
	
	$scope.$watch('filter', function(){
		clearTimeout($scope.filterHandler);
		$scope.filterHandler = setTimeout(function(){
			console.log("filter => ", $scope.filter);
			getEstimationsCount();
		}, 500);
	}, true);
	
	$scope.openFrom = function($event) {
	    $event.preventDefault();
	    $event.stopPropagation();

	    $scope.openedFrom = true;
	};
	
	$scope.openTo = function($event) {
	    $event.preventDefault();
	    $event.stopPropagation();

	    $scope.openedTo = true;
	};
	
	function createEstimationUrls(estimations){
		_.map(estimations, function(estimation){estimation.href = $scope.urlsEstimationsShow.replace('0', estimation.id);});
		return estimations;
	}
	
	function getEstimations()
	{
		$scope.filter.offset = $scope.itemsPerPage*($scope.currentPage - 1);
		$scope.filter.limit = $scope.itemsPerPage;
		$http({
			method: "GET", 
			url: $scope.urlsEstimationsNg,
			params: $scope.filter
			  })
		.success(function(response){
			console.log(" estimations => ",response);
			if (response.result)
			{
				$scope.estimations = createEstimationUrls(response.result);
			}
		})
	}
	
	function getEstimationsCount()
	{
		$http({
			method: "GET", 
			url: $scope.urlsEstimationsCountNg,
			params: $scope.filter
			  })
		.success(function(response){
			console.log(response);
			if (response.result)
			{
				$scope.totalItems = parseInt(response.result);
				getEstimations();
			}
		})
	}
	getEstimationsCount();
	
	$scope.removeEstimation = function(estimation){
		var remove = confirm("Дійсно бажаєте видалити кошторис № " + estimation.id + " ?");
		if (!remove) return;
		
		var url = $scope.urlsEstimationsRemoveNg.replace('0', estimation.id);
		
		$http({
			method: "GET", 
			url: url
			  })
		.success(function(response){
			console.log(response);
			if (response.result)
			{
				getEstimationsCount();
				var index = _.indexOf(_.map($scope.tabs, function(tab){return tab.estimationid;}), estimation.id);
				if (index != undefined) $scope.tabs.splice(index, 1);
			}
		});
	}
	
	///-----------------Cost Categories-------------------------
	$scope.urlsCostCategoriesGetNg = URLS.costCategoriesGetNg;
	$scope.urlsCostCategoriesRemoveNg = URLS.costCategoriesRemoveNg;
	$scope.costCategoriesWorks = [];
	$scope.costCategoriesMaterials = [];
	
	function getCostCategoriesWorks(){
		$http({
			method: "GET", 
			url: $scope.urlsCostCategoriesGetNg,
			params: {type: 'works'}
			  })
		.success(function(response){
			console.log('costCategoriesWorks => ', response.result);
			if (response.result)
			{
				$scope.costCategoriesWorks = response.result;
			}
		})
	};
	getCostCategoriesWorks();
	
	function getCostCategoriesMaterials(){
		$http({
			method: "GET", 
			url: $scope.urlsCostCategoriesGetNg,
			params: {type: 'materials'}
			  })
		.success(function(response){
			console.log('costCategoriesMaterials => ', response.result);
			if (response.result)
			{
				$scope.costCategoriesMaterials = response.result;
			}
		})
	};
	getCostCategoriesMaterials();
	
	$scope.addCostCategory = function(type){
		var modalInstance = $modal.open({
		      templateUrl: 'addCostCategory.html',
		      controller: 'AddCostCategoryController',
		      backdrop: "static",
		      resolve: {
		    	  type: function(){return type;}
		      }
		});
		
		modalInstance.result.then(function (added) {
		      if (added) {
		    	  switch (added.type){
			    	  case 'works': getCostCategoriesWorks(); break;
			    	  case 'materials': getCostCategoriesMaterials(); break;
		    	  }
		      }
		    }, function () {
		      //bad
		});
	}
	
	$scope.editCostCategory = function(costCategory){
		var modalInstance = $modal.open({
		      templateUrl: 'editCostCategory.html',
		      controller: 'EditCostCategoryController',
		      backdrop: "static",
		      resolve: {
		    	  costCategory: function(){return costCategory;}
		      }
		});
		
		modalInstance.result.then(function (edited) {
		      if (edited) {
		    	  switch (edited.type){
		    	  	case 'works': getCostCategoriesWorks(); break;
		    	  	case 'materials': getCostCategoriesMaterials(); break;
		    	  }
		      }
		    }, function () {
		      //bad
		});
	}
	
	$scope.removeCostCategory = function(costCategory){
		var remove = confirm("Дійсно бажаєте видалити: " + costCategory.name + " ?\nУВАГА! Усі дочірні елементи будуть також видалені!");
		if (!remove) return;
		
		var url = $scope.urlsCostCategoriesRemoveNg.replace('0', costCategory.id);
		
		$http({
			method: "GET", 
			url: url
			  })
		.success(function(response){
			console.log(response);
			if (response.result)
			{
				switch(costCategory.type){
					case 'works': getCostCategoriesWorks(); break;
					case 'materials': getCostCategoriesMaterials(); break;
				}
			}
		});
	}
	///---------------------Costs-------------------------------
	$scope.urlsCostsRemoveNg = URLS.costsRemoveNg;
	
	$scope.addCost = function(category){
		var modalInstance = $modal.open({
		      templateUrl: 'addCost.html',
		      controller: 'AddCostController',
		      backdrop: "static",
		      resolve: {
		    	  category: function(){return category;}
		      }
		});
		
		modalInstance.result.then(function (added) {
		      if (added) {
		    	  switch (added.categoryType){
			    	  case 'works': getCostCategoriesWorks(); break;
			    	  case 'materials': getCostCategoriesMaterials(); break;
		    	  }
		      }
		    }, function () {
		      //bad
		});
	}
	
	$scope.editCost = function(cost){
		var modalInstance = $modal.open({
		      templateUrl: 'editCost.html',
		      controller: 'EditCostController',
		      backdrop: "static",
		      resolve: {
		    	  cost: function(){return cost;}
		      }
		});
		
		modalInstance.result.then(function (edited) {
		      if (edited) {
		    	  switch (edited.categoryType){
		    	  	case 'works': getCostCategoriesWorks(); break;
		    	  	case 'materials': getCostCategoriesMaterials(); break;
		    	  }
		      }
		    }, function () {
		      //bad
		});
	}
	$scope.removeCost = function(cost){
		var remove = confirm("Дійсно бажаєте видалити: " + cost.name + " ?");
		if (!remove) return;
		
		var url = $scope.urlsCostsRemoveNg.replace('0', cost.id);
		
		$http({
			method: "GET", 
			url: url
			  })
		.success(function(response){
			console.log(response);
			if (response.result)
			{
				switch(cost.categoryType){
					case 'works': getCostCategoriesWorks(); break;
					case 'materials': getCostCategoriesMaterials(); break;
				}
			}
		});
	}
	///---------------Estimation Costs--------------------------
	$scope.urlsEstimationCostsAddNg = URLS.estimationCostsAddNg;
	
	$scope.addEstimationCost = function(cost){
		var tab = _.find($scope.tabs, function(tab){return tab.active;});
		if (tab == undefined) {
			alert('Нема активних кошторисів!');
			return;
		}
		
		if (tab.estimationid == undefined) {
			alert('Введіть спочатку поля: Замовник, Виконавець !');
			return;
		}
		
		$http({
			method: "POST", 
			url: $scope.urlsEstimationCostsAddNg,
			data: {estimationid: tab.estimationid, costid: cost.id}
			  })
		.success(function(response){
			console.log("added estimation cost => ", response);
			if (response.result)
			{
				tab.refreshEstimation();
			}
		})
	}
	///-------------------Tabs----------------------------------
	$scope.tabs = [];
	
	$scope.addTab = function(esimationid){
		var data = {
				title: 'не збережений'
		};
		
		if (esimationid != undefined) {
			var tab = _.find($scope.tabs, function(tab){return tab.estimationid == esimationid;})
			
			if (tab != undefined){
				tab.active = true;
				return;
			}else{
				data.estimationid = esimationid;
			}
		}
		
		$scope.tabs.push(data);
	}
	
	$scope.closeTab = function(index, event){
		event.preventDefault();
		event.stopPropagation();
		
		var close = confirm("Дійсно бажаєте закрити: № " + $scope.tabs[index].title + " ?");
		if (!close) return;
		
		$scope.tabs.splice(index, 1);
	}
	///---------------------------------------------------------
})
.controller('AddCostCategoryController', function($scope,$http,$modalInstance,type){
	console.log('AddCostCategoryController loaded!');
	
	$scope.urlsCostCategoriesAddNg = URLS.costCategoriesAddNg;
	
	$scope.costCategory = {};
	$scope.costCategory.type = type;
	
	function addCostCategory(){
		$http({
			method: "POST", 
			url: $scope.urlsCostCategoriesAddNg,
			data: $scope.costCategory
			  })
		.success(function(response){
			console.log("added cost category => ", response);
			if (response.result)
			{
				$modalInstance.close(response.result);
			}
		})
	}
	
	$scope.ok = function () {
		if (!$scope.costCategoryForm.$valid) return;
		addCostCategory();
	};

	$scope.cancel = function () {
	    $modalInstance.dismiss('cancel');
	};
})
.controller('EditCostCategoryController', function($scope,$http,$modalInstance,costCategory){
	console.log('EditCostCategoryController loaded!');
	
	$scope.urlsCostCategoriesEditNg = URLS.costCategoriesEditNg;
	$scope.costCategory = costCategory;
	
	function editCostCategory(){
		var url = $scope.urlsCostCategoriesEditNg.replace('0', $scope.costCategory.id);
		
		$http({
			method: "POST", 
			url: url,
			data: $scope.costCategory
			  })
		.success(function(response){
			console.log("edited cost category => ", response);
			if (response.result)
			{
				$modalInstance.close(response.result);
			}
		})
	}
	
	$scope.ok = function () {
		if (!$scope.costCategoryForm.$valid) return;
		editCostCategory();
	};

	$scope.cancel = function () {
	    $modalInstance.dismiss('cancel');
	};
})
.controller('AddCostController', function($scope,$http,$modalInstance,category){
	console.log('AddCostController loaded!');
	
	$scope.urlsCostsAddNg = URLS.costsAddNg;
	
	$scope.cost = {};
	$scope.cost.categoryid = category.id;
	
	function addCost(){
		$http({
			method: "POST", 
			url: $scope.urlsCostsAddNg,
			data: $scope.cost
			  })
		.success(function(response){
			console.log("added cost => ", response);
			if (response.result)
			{
				$modalInstance.close(response.result);
			}
		})
	}
	
	$scope.ok = function () {
		if (!$scope.costForm.$valid) return;
		addCost();
	};

	$scope.cancel = function () {
	    $modalInstance.dismiss('cancel');
	};
})
.controller('EditCostController', function($scope,$http,$modalInstance,cost){
	console.log('EditCostController loaded!');
	
	$scope.urlsCostsEditNg = URLS.costsEditNg;
	$scope.cost = cost;
	
	function editCost(){
		var url = $scope.urlsCostsEditNg.replace('0', $scope.cost.id);
		
		$http({
			method: "POST", 
			url: url,
			data: $scope.cost
			  })
		.success(function(response){
			console.log("edited cost => ", response);
			if (response.result)
			{
				$modalInstance.close(response.result);
			}
		})
	}
	
	$scope.ok = function () {
		if (!$scope.costForm.$valid) return;
		editCost();
	};

	$scope.cancel = function () {
	    $modalInstance.dismiss('cancel');
	};
})
.controller('TabController', function($scope, $http){
	console.log('TabController loaded!');
	
	$scope.urlsEstimationsSaveNg = URLS.estimationsSaveNg;
	$scope.urlsEstimationsGetNg = URLS.estimationsGetNg;
	$scope.urlsEstimationsShow = URLS.estimationsShow;
	$scope.urlsEstimationsCsv = URLS.estimationsCsv;
	$scope.urlsEstimationCostsRemoveNg = URLS.estimationCostsRemoveNg;
	$scope.urlsEstimationCostsEditNg = URLS.estimationCostsEditNg;
	
	$scope.estimationChangesHandler = null;
	$scope.estimationCostChangesHandler = {};
	$scope.estimation = {
			performer: 'ТОВ "РЕНОВЕЙТ"'
	};
	$scope.tab.active = true;
	
	$scope.tab.refreshEstimation = function(){
		getEstimation($scope.tab.estimationid);
		showAlert();
	};
	
	(function checkEstimation(){
		if ($scope.tab.estimationid != undefined) getEstimation($scope.tab.estimationid);
	})();
	
	function createEstimationUrl(id){
		return $scope.urlsEstimationsShow.replace('0', id);
	}
	
	function createEstimationCsv(id){
		return $scope.urlsEstimationsCsv.replace('0', id);
	}
	
	function createCostCategories(estimationCosts){
		var categories = [];
		
		_.map(estimationCosts, function(estimationCost){
			if (_.indexOf(_.map(categories, function(category){return category.name;}),estimationCost.cost.categoryType) == -1){
				categories.push({name:estimationCost.cost.categoryType, items: []});
			}
			
			var category = _.find(categories, function(category){return category.name == estimationCost.cost.categoryType;});
			category.items.push(estimationCost);
		});
		
		return categories;
	}
	
	function copyEstimation(resource){
		$scope.estimation.id = resource.id;
		$scope.estimation.customer = resource.customer;
		$scope.estimation.performer = resource.performer;
		$scope.estimation.estimationCosts = resource.estimationCosts;
		$scope.estimation.worksAmount = resource.worksAmount;
		$scope.estimation.materialsAmount = resource.materialsAmount;
		$scope.estimation.totalAmount = resource.totalAmount;
		$scope.estimation.costCategories = createCostCategories($scope.estimation.estimationCosts);
		$scope.estimation.href = createEstimationUrl($scope.estimation.id);
		$scope.estimation.csv = createEstimationCsv($scope.estimation.id);
		
		$scope.tab.title = $scope.estimation.id;
		$scope.tab.estimationid = $scope.estimation.id;
	}
	
	function getEstimation(id){
		var url = $scope.urlsEstimationsGetNg.replace('0', id);
		
		$http({
			method: "GET", 
			url: url
			  })
		.success(function(response){
			console.log("got estimation => ", response);
			if (response.result)
			{
				copyEstimation(response.result);
			}
		})
	};
	
	function showAlert(){
		$("#infoAlert").fadeIn(2000, function(){$("#infoAlert").fadeOut(2000);});
	};
	
	function saveEstimation(){
		$http({
			method: "POST", 
			url: $scope.urlsEstimationsSaveNg,
			data: $scope.estimation
			  })
		.success(function(response){
			console.log("saved estimation => ", response);
			if (response.result)
			{
				copyEstimation(response.result);
				showAlert();
			}
		})
	}
	
	$scope.fireEstimationChanges = function(){
		if ((!$scope.estimation.customer) || (!$scope.estimation.performer)) return;
		
		clearTimeout($scope.estimationChangesHandler);
		$scope.estimationChangesHandler = setTimeout(function(){
			saveEstimation();
		}, 2000);
	}
	
	$scope.fireEstimationCostChanges = function(estimationCost){
		estimationCost.total=estimationCost.cost.price*estimationCost.count;
		
		clearTimeout($scope.estimationCostChangesHandler[estimationCost.id]);
		$scope.estimationCostChangesHandler[estimationCost.id] = setTimeout(function(){
			editEstimationCost(estimationCost);
			delete $scope.estimationCostChangesHandler[estimationCost.id];
		}, 1000);
	}
	
	
	$scope.removeEstimationCost = function(estimationCost){
		var remove = confirm("Дійсно бажаєте видалити статтю витрат: " + estimationCost.cost.name + " ?");
		if (!remove) return;
		
		var url = $scope.urlsEstimationCostsRemoveNg.replace('0', estimationCost.id);
		
		$http({
			method: "GET", 
			url: url
			  })
		.success(function(response){
			console.log(response);
			if (response.result)
			{
				$scope.tab.refreshEstimation();
			}
		});
	}
	
	function editEstimationCost(estimationCost){
		var url = $scope.urlsEstimationCostsEditNg.replace('0', estimationCost.id);
		
		$http({
			method: "POST", 
			url: url,
			data: estimationCost
			  })
		.success(function(response){
			console.log(response);
			if (response.result)
			{
				if ($.isEmptyObject($scope.estimationCostChangesHandler)){
					$scope.tab.refreshEstimation();
				}
			}
		});
	}
});