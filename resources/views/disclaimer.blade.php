@extends('layouts.sidebar-left-layout')

@section('content')
    <div class="w-full flex flex-col gap-4">
        <h3 class="font-medium text-2xl text-gray-200">Disclaimer</h3>
        <article>
            <section>
                <h4 class="text-gray-300">
                    Information according to §5 TMG (German Telemedia Act):
                </h4>
                <ul class="list-disc list-inside text-gray-300">
                    <li>Tobias Hopp</li>
                    <li>Oberer Markenweg 70</li>
                    <li>56566 Neuwied</li>
                    <li>Germany</li>
                    <li>Contact: <a href="mailto:mail@tobias-hopp.de" class="hover:underline"
                            titlte="E-Mail">mail@tobias-hopp.de</a></li>
                </ul>
                <p class="text-gray-300">
                    This API is provided as a personal service for experimentation, development, and limited use.
                    It is not offered as a guaranteed commercial product or enterprise-grade service.
                </p>
            </section>
            <section>
                <h4 class="font-medium text-xl text-gray-200">Terms of Use</h4>
                <p class="text-gray-300">
                    This API is provided on an “as is” and “as available” basis, without warranties of any kind,
                    whether express or implied. I do not guarantee that the API will be uninterrupted, secure,
                    error-free, accurate, or suitable for any particular purpose.
                </p>
            </section>
            <section>
                <h4>Access and Availability</h4>
                <p class="text-gray-300">
                    Access to this API may be changed, restricted, suspended, or discontinued at any time,
                    with or without notice. I am under no obligation to maintain uptime, backward compatibility,
                    or continued support for any endpoint or feature.
                </p>
            </section>
            <section>
                <h4>Intended Use</h4>
                <p class="text-gray-300">
                    This API is intended for personal use, experimentation, and development. It is not designed for
                    high-volume production use, commercial applications, or critical systems.
                </p>
            </section>
            <section>
                <h4>Rate Limits and Access</h4>
                <p class="text-gray-300">
                    API access may be subject to informal or technical limits, including rate limits, access
                    restrictions,
                    or key revocation. I reserve the right to block or remove access at my sole discretion,
                    especially in cases of misuse or excessive requests.
                </p>
            </section>
            <section>
                <h4>Data and Privacy</h4>
                <p class="text-gray-300">
                    Please do not submit sensitive, confidential, or personal data unless explicitly required and
                    properly protected.
                    While reasonable care may be taken, I cannot guarantee absolute security of transmitted or stored
                    data.
                </p>
            </section>
            <section>
                <h4>Limitation of Liability</h4>
                <p class="text-gray-300">
                    To the fullest extent permitted by applicable law, I shall not be liable for any direct, indirect,
                    incidental, consequential, or special damages arising out of or related to the use of, or inability
                    to use,
                    this API.
                </p>
            </section>
            <section>
                <h4 class="font-medium text-xl text-gray-200">Changes</h4>
                <p class="text-gray-300">
                    I may update this disclaimer, the API behavior, or any related documentation at any time without
                    prior
                    notice.
                    Continued use of the API after changes are made constitutes acceptance of those changes.
                </p>
            </section>
        </article>
    </div>
@endsection
