define( [ "jquery" ], function( $ ) {
	"use strict";

	var PasswordValidator = {};

	PasswordValidator.view = $( "#validator-view" );
	PasswordValidator.field = $( "#validator-field" );
	PasswordValidator.currentPassword = $( "#t3-password" );
	PasswordValidator.rules = PasswordValidator.view.find( ".rule" );

	PasswordValidator.reset = function() {
		PasswordValidator.rules.each( function() {
			var icons = $( this ).find( ".icon" );

			icons.eq( 0 ).show();
			icons.eq( 1 ).hide();
		} );
	};

	PasswordValidator.validatePasswordLength = function() {
		var rule = PasswordValidator.view.find( ".rule[data-validator-rule='passwordLength']" ),
			minimum = rule.attr( "data-validator-rule-minimum" );

		if ( rule.length === 0 ) {
			return;
		}

		if ( PasswordValidator.field.val().length >= minimum ) {
			rule.find( ".icon" ).eq( 0 ).hide();
			rule.find( ".icon" ).eq( 1 ).show();
		}
	};

	PasswordValidator.validateSpecialChar = function() {
		var rule = PasswordValidator.view.find( ".rule[data-validator-rule='specialChar']" ),
			minimum = rule.attr( "data-validator-rule-minimum" );

		if ( rule.length === 0 ) {
			return;
		}

		var matches = PasswordValidator.field.val().match( /[!$%&\/=?,.]/g );

		if ( matches !== null && matches.length >= minimum ) {
			rule.find( ".icon" ).eq( 0 ).hide();
			rule.find( ".icon" ).eq( 1 ).show();
		}
	};

	PasswordValidator.validateDigit = function() {
		var rule = PasswordValidator.view.find( ".rule[data-validator-rule='digit']" ),
			minimum = rule.attr( "data-validator-rule-minimum" );

		if ( rule.length === 0 ) {
			return;
		}

		var matches = PasswordValidator.field.val().match( /[0-9]/g );

		if ( matches !== null && matches.length >= minimum ) {
			rule.find( ".icon" ).eq( 0 ).hide();
			rule.find( ".icon" ).eq( 1 ).show();
		}
	};

	PasswordValidator.validateCapitalChar = function() {
		var rule = PasswordValidator.view.find( ".rule[data-validator-rule='capitalChar']" ),
			minimum = rule.attr( "data-validator-rule-minimum" );

		if ( rule.length === 0 ) {
			return;
		}

		var matches = PasswordValidator.field.val().match( /[A-ZÄÖÜ]/g );

		if ( matches !== null && matches.length >= minimum ) {
			rule.find( ".icon" ).eq( 0 ).hide();
			rule.find( ".icon" ).eq( 1 ).show();
		}
	};

	PasswordValidator.validateLowercaseChar = function() {
		var rule = PasswordValidator.view.find( ".rule[data-validator-rule='lowercaseChar']" ),
			minimum = rule.attr( "data-validator-rule-minimum" );

		if ( rule.length === 0 ) {
			return;
		}

		var matches = PasswordValidator.field.val().match( /[a-zäöü]/g );

		if ( matches !== null && matches.length >= minimum ) {
			rule.find( ".icon" ).eq( 0 ).hide();
			rule.find( ".icon" ).eq( 1 ).show();
		}
	};

	PasswordValidator.validateDifferentPasswords = function() {
		var rule = PasswordValidator.view.find( ".rule[data-validator-rule='differentPasswords']" );

		if ( PasswordValidator.currentPassword.val() !== '' &&
			PasswordValidator.currentPassword.val() !== PasswordValidator.field.val() ) {
			rule.find( ".icon" ).eq( 0 ).hide();
			rule.find( ".icon" ).eq( 1 ).show();
		}
	};

	PasswordValidator.init = function() {
		PasswordValidator.reset();
		PasswordValidator.view.hide();
		PasswordValidator.view.removeClass( "hidden" );

		PasswordValidator.field.on( "focus", function() {
			PasswordValidator.view.slideDown( 500 );
		} );

		PasswordValidator.field.on( "blur", function() {
			PasswordValidator.view.slideUp( 500 );
		} );

		PasswordValidator.field.on( "keyup", function() {
			PasswordValidator.reset();
			PasswordValidator.validatePasswordLength();
			PasswordValidator.validateSpecialChar();
			PasswordValidator.validateDigit();
			PasswordValidator.validateCapitalChar();
			PasswordValidator.validateLowercaseChar();
			PasswordValidator.validateDifferentPasswords();
		} );
	};

	$( function() {
		PasswordValidator.init();
	} );

	return PasswordValidator;
} );
