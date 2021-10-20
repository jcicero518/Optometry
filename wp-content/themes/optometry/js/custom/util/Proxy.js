export function createProxy( subject ) {
	let proto = Object.getPrototypeOf( subject );

	function Proxy( subject ) {
		this.subject = subject;
	}
	Proxy.prototype = Object.create( proto );

	// proxied method
	Proxy.prototype.hello = () => {
		return this.subject.hello() + ' world';
	};

	// delegated method
	Proxy.prototype.goodbye = () => {
		return this.subject.apply( this.subject, arguments );
	};

	return new Proxy( subject );
}