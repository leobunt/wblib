	<div class="fbradiogroup fb{{type}}">
    {{ :loop options }}
    <span class="fbopt">
      <input {{ attributes }} {{ checked }}{{ :if tooltip }} tooltipText="{{ tooltip }}"{{ :ifend }} />
	    <label for="{{ id }}">{{ text }}</label>
		{{ :if break }}<br />{{ :ifend }}
	</span>
    {{ :loopend }}
    </div>
