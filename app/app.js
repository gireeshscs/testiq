var app = angular.module('myApp', ['ngRoute']);

app.factory("services", ['$http', function($http) {
  var serviceBase = 'services/'
    var obj = {};
    obj.getStudents = function(){
        return $http.get(serviceBase + 'students');
    }
    obj.getStudent = function(studentID){
        return $http.get(serviceBase + 'student?id=' + studentID);
    }
     obj.insertStudent = function (student) {
    return $http.post(serviceBase + 'insertStudent', student).then(function (results) {
        return results;
    });
    };
    obj.updateStudent = function (id,student) {
	    return $http.post(serviceBase + 'updateStudent', {id:id, student:student}).then(function (status) {
	        return status.data;
	    });
	};
    
    obj.deleteStudent = function (id) {
	    return $http.delete(serviceBase + 'deleteStudent?id=' + id).then(function (status) {
	        return status.data;
	    });
	};

    
//Tag  object functions	
   obj.getTags = function(){
        return $http.get(serviceBase + 'tags');
    }
    obj.getTag = function(tagID){
        return $http.get(serviceBase + 'tag?id=' + tagID);
    }
  
    obj.insertTag = function (tag) {
    return $http.post(serviceBase + 'insertTag', tag).then(function (results) {
        return results;
    });
	};
	
   obj.updateTag = function (id,tag) {
	    return $http.post(serviceBase + 'updateTag', {id:id, tag:tag}).then(function (status) {
	        return status.data;
	    });
	};

    obj.deleteTag = function (id) {
	    return $http.delete(serviceBase + 'deleteTag?id=' + id).then(function (status) {
	        return status.data;
	    });
	};
	

//Subject  object functions

    obj.getSubjects = function(){
        return $http.get(serviceBase + 'subjects');
    }
    obj.getSubject = function(subjectID){
        return $http.get(serviceBase + 'subject?id=' + subjectID);
    }
  
    obj.insertSubject = function (subject) {
    return $http.post(serviceBase + 'insertSubject', subject).then(function (results) {
        return results;
    });
	};
	
   obj.updateSubject = function (id,subject) {
	    return $http.post(serviceBase + 'updateSubject', {id:id, subject:subject}).then(function (status) {
	        return status.data;
	    });
	};

    obj.deleteSubject = function (id) {
	    return $http.delete(serviceBase + 'deleteSubject?id=' + id).then(function (status) {
	        return status.data;
	    });
	};


	
//Question  object functions

    obj.getQuestions = function(){
        return $http.get(serviceBase + 'questions');
    }
    obj.getQuestion = function(questionID){
        return $http.get(serviceBase + 'question?id=' + questionID);
    }
  
    obj.insertQuestion = function (question) {
    return $http.post(serviceBase + 'insertQuestion', question).then(function (results) {
        return results;
    });
	};
	
   obj.updateQuestion = function (id,question) {
	    return $http.post(serviceBase + 'updateQuestion', {id:id, question:question}).then(function (status) {
	        return status.data;
	    });
	};

    obj.deleteQuestion = function (id) {
	    return $http.delete(serviceBase + 'deleteQuestion?id=' + id).then(function (status) {
	        return status.data;
	    });
	};
	
      obj.getTagOptions = function(){
        return $http.get(serviceBase + 'getTagOptions');
    };
    
     obj.getSubjectOptions = function(){
        return $http.get(serviceBase + 'getSubjectOptions');
    };

    
    return obj;   
}]);

app.controller('listCtrl', function ($scope, services) {
    services.getStudents().then(function(data){
        $scope.students = data.data;
    });
});

app.controller('listTagCtrl', function ($scope, services) {
    services.getTags().then(function(data){
		//alert(data.data);
	    $scope.tags = data.data;
    });
});

app.controller('listSubjectCtrl', function ($scope, services) {
    services.getSubjects().then(function(data){
        $scope.subjects = data.data;
    });
});

app.controller('listQuestionCtrl', function ($scope, services) {
    services.getQuestions().then(function(data){
        $scope.questions = data.data;
    });
});


app.controller('tagOptionCtrl', function ($scope, services,$window) {
    services.getTagOptions().then(function(data){
	//alert(data.data);
        $scope.tagoptions = data.data;	    
	});
	
	$scope.tagChanged = function () {
		$scope.question.tag = JSON.stringify($scope.question.tag);
	};
    
});

//angular.element(document).ready(function() {
  //$('input[tag]:visible:first').focus();
//});


app.controller('subjectOptionCtrl', function ($scope, services) {
    services.getSubjectOptions().then(function(data){
	//alert(data.data);
        $scope.subjectoptions = data.data;	    
    });
    
	$scope.subjectChanged = function () {
		$scope.question.subject = JSON.stringify($scope.question.subject);
	};
});

/*
app.controller('MyCtrl', function ($scope, services) {
  $scope.tag = "Das ist ein Test";
    $scope.colors = [{name: 'black',shade: 'dark'},{name: 'white',shade: 'light'},{name: 'red',shade: 'dark'},{name: 'blue',        shade: 'dark'},{name: 'yellow', shade: 'light'}
    ];

    $scope.selectedColors = [$scope.colors[1],$scope.colors[3]];

});	    
*/	    

app.controller('test', function($scope) {
    $scope.fields = [
      { value: '' }
    ];
    
    $scope.add = function () {
      $scope.fields.push({
        value: ''
      });
     // $scope('btn1').show();
    };
    
    $scope.remove = function (index) {
      $scope.fields.splice(index, 1);
    };
  });


  
app.controller('editCtrl', function ($scope, $rootScope, $location, $routeParams, services, student) {
    var studentID = ($routeParams.studentID) ? parseInt($routeParams.studentID) : 0;
    $rootScope.title = (studentID > 0) ? 'Edit Student' : 'Add Student';
    $scope.buttonText = (studentID > 0) ? 'Update Student' : 'Add New Student';
      var original = student.data;
      original._id = studentID;
      $scope.student = angular.copy(original);
      $scope.student._id = studentID;

      $scope.isClean = function() {
        return angular.equals(original, $scope.student);
      }

      $scope.deleteStudent = function(student) {
        $location.path('/');
        if(confirm("Are you sure to delete student number: "+$scope.student._id)==true)
        services.deleteStudent(student.studentNumber);
      };

      $scope.saveStudent = function(student) {
        $location.path('/');
        if (studentID <= 0) {
            services.insertStudent(student);
        }
        else {
            services.updateStudent(studentID, student);
        }
    };
});



app.controller('editTagCtrl', function ($scope, $rootScope, $location, $routeParams, services, tag) {
    var tagID = ($routeParams.tagID) ? parseInt($routeParams.tagID) : 0;
    $rootScope.title = (tagID > 0) ? 'Edit Tag' : 'Add Tag';
    $scope.buttonText = (tagID > 0) ? 'Update Tag' : 'Add New Tag';
      var original = tag.data;
      original._id = tagID;
      $scope.tag = angular.copy(original);
      $scope.tag._id = tagID;

      $scope.isClean = function() {
        return angular.equals(original, $scope.tag);
      }

      $scope.deleteTag = function(tag) {
        $location.path('/tags');
        if(confirm("Are you sure to delete tag number: "+$scope.tag._id)==true)
        services.deleteTag(tag.tag_id);
      };

      $scope.saveTag = function(tag) {
        $location.path('/tags');
        if (tagID <= 0) {
            services.insertTag(tag);
        }
        else {
            services.updateTag(tagID, tag);
        }
    };
});



app.controller('editSubjectCtrl', function ($scope, $rootScope, $location, $routeParams, services, subject) {
    var subjectID = ($routeParams.subjectID) ? parseInt($routeParams.subjectID) : 0;
    $rootScope.title = (subjectID > 0) ? 'Edit Subject' : 'Add Subject';
    $scope.buttonText = (subjectID > 0) ? 'Update Subject' : 'Add New Subject';
      var original = subject.data;
      original._id = subjectID;
      $scope.subject = angular.copy(original);
      $scope.subject._id = subjectID;

      $scope.isClean = function() {
        return angular.equals(original, $scope.subject);
      }

      $scope.deleteSubject = function(subject) {
        $location.path('/subjects');
        if(confirm("Are you sure to delete subject number: "+$scope.subject._id)==true)
        services.deleteSubject(subject.subject_id);
      };

      $scope.saveSubject = function(subject) {
        $location.path('/subjects');
        if (subjectID <= 0) {
            services.insertSubject(subject);
        }
        else {
            services.updateSubject(subjectID, subject);
        }
    };
});



app.controller('editQuestionCtrl', function ($scope, $rootScope, $location, $routeParams, services, question) {
    var questionID = ($routeParams.questionID) ? parseInt($routeParams.questionID) : 0;
    $rootScope.title = (questionID > 0) ? 'Edit Question' : 'Add Question';
    $scope.buttonText = (questionID > 0) ? 'Update Question' : 'Add New Question';
	
	
      var original = question.data;
      original._id = questionID;
      $scope.question = angular.copy(original);
      $scope.question._id = questionID;

      $scope.isClean = function() {
        return angular.equals(original, $scope.question);
      }
      
      $scope.deleteQuestion = function(question) {
        $location.path('/questions');
        if(confirm("Are you sure to delete question : "+$scope.question._id)==true)
        services.deleteQuestion(question.question_id);
      };

      $scope.saveQuestion = function(question) {
        $location.path('/questions');
        if (questionID <= 0) {
	//	alert("sadasdasd");
            services.insertQuestion(question);
        }
        else {
		//alert(questionID);
		
// $scope.focus = function () {
    
  //  console.log($("#question1"));
  // }
            services.updateQuestion(questionID, question);
		
        }
    };
});

/*
//angular.module('ui.bootstrap.demo', ['ngAnimate', 'ui.bootstrap']);
app.controller('ButtonsCtrl', function ($scope) {
  //$scope.singleModel = 1;

  $scope.radioModel = 'Medium';

  $scope.checkModel = {
    low: false,
    medium: true,
    high: false
  };

  $scope.checkResults = [];

  $scope.$watchCollection('checkModel', function () {
    $scope.checkResults = [];
    angular.forEach($scope.checkModel, function (value, key) {
      if (value) {
        $scope.checkResults.push(key);
      }
    });
  });
});

*/

app.controller('complexityCtrl', function($scope) {
    $scope.items = [
        { name: 'Low' ,value: 1},
        {name: 'Medium',value: 2},
        { name: 'High', value: 3 }
    ];
    $scope.complexity = null;
});

app.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/students', {
        title: 'Students',
        templateUrl: 'partials/students.html',
        controller: 'listCtrl'
      })
      .when('/tags', {
        title: 'Tags',
        templateUrl: 'partials/tag_list.html',
        controller: 'listTagCtrl'
      })
     .when('/edit-tag/:tagID', {
        title: 'Edit Tag',
        templateUrl: 'partials/edit_tag.html',
        controller: 'editTagCtrl',
        resolve: {
          tag: function(services, $route){
            var tagID = $route.current.params.tagID;
            return services.getTag(tagID);
          }
        }
      })
	.when('/subjects', {
        title: 'Subjects',
        templateUrl: 'partials/subject_list.html',
        controller: 'listSubjectCtrl'
      })
     .when('/edit-subject/:subjectID', {
        title: 'Edit Subject',
        templateUrl: 'partials/edit_subject.html',
        controller: 'editSubjectCtrl',
        resolve: {
          subject: function(services, $route){
            var subjectID = $route.current.params.subjectID;
            return services.getSubject(subjectID);
          }
        }
      })
      
      .when('/questions', {
        title: 'Questions',
        templateUrl: 'partials/questions.html',
        controller: 'listQuestionCtrl'
      })
     .when('/edit-question/:questionID', {
        title: 'Edit Question',
        templateUrl: 'partials/edit_question.html',
        controller: 'editQuestionCtrl',
        resolve: {
          question: function(services, $route){
            var questionID = $route.current.params.questionID;
            return services.getQuestion(questionID);
          }
        }
      })
      
      
      .when('/edit-student/:studentID', {
        title: 'Edit Students',
        templateUrl: 'partials/edit-student.html',
        controller: 'editCtrl',
        resolve: {
          student: function(services, $route){
            var studentID = $route.current.params.studentID;
            return services.getStudent(studentID);
          }
        }
      })
      
      
      //Login and signup 
      
      .when('/login', {
            title: 'Login',
            templateUrl: 'partials/login.html',
            controller: 'authCtrl'
        })
            .when('/logout', {
                title: 'Logout',
                templateUrl: 'partials/login.html',
                controller: 'logoutCtrl'
            })
            .when('/signup', {
                title: 'Signup',
                templateUrl: 'partials/signup.html',
                controller: 'authCtrl'
            })
            .when('/dashboard', {
                title: 'Dashboard',
                templateUrl: 'partials/dashboard.html',
                controller: 'authCtrl'
            })
            .when('/', {
                title: 'Login',
                templateUrl: 'partials/login.html',
                controller: 'authCtrl',
                role: '0'
            })
      
      
      // End login and signup
      
      
      .otherwise({
        redirectTo: '/'
      });
}]);

/*
app.directive('autoFocus', function($timeout) {
    return {
        link: function (scope, element, attrs) {
            attrs.$observe("autoFocus", function(newValue){
                if (newValue === "true")
                    $timeout(function(){element.focus()});
            });
        }
    };
});
*/

app.run(['$location', '$rootScope', function($location, $rootScope) {
    $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
        $rootScope.title = current.$$route.title;
    });
}]);
